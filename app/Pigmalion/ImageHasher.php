<?php

namespace App\Pigmalion;

/**
 * Perceptual image comparison using dHash (structural) and
 * color pixel comparison at low resolution (16x16 or 16xH).
 *
 * Three matching strategies, progressively more expensive:
 *   1. dHash      — 9x8 grayscale gradient, Hamming distance (fastest)
 *   2. Dimensions — aspect ratio + size check (fast pre-filter)
 *   3. Color      — RGB comparison at 16xN grid (accurate, preserves AR)
 */
class ImageHasher
{
    /**
     * Compute the 64-bit dHash of an image file.
     */
    public static function hash(string $imagePath): string
    {
        if (! file_exists($imagePath)) {
            return '';
        }

        $fileSize = filesize($imagePath);
        if ($fileSize > 10 * 1024 * 1024) {
            return '';
        }

        try {
            $image = @imagecreatefromstring(file_get_contents($imagePath));
        } catch (\Throwable $e) {
            return '';
        }

        if (! $image) {
            return '';
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if ($width < 2 || $height < 2) {
            imagedestroy($image);

            return '';
        }

        $resized = imagecreatetruecolor(9, 8);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, 9, 8, $width, $height);
        imagedestroy($image);

        $hash = 0;
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $left = self::gray(imagecolorat($resized, $x, $y));
                $right = self::gray(imagecolorat($resized, $x + 1, $y));

                if ($left > $right) {
                    $hash |= (1 << ($y * 8 + $x));
                }
            }
        }

        imagedestroy($resized);

        return sprintf('%016x', $hash);
    }

    private static function gray(int $rgb): int
    {
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        return (int) ($r * 0.299 + $g * 0.587 + $b * 0.114);
    }

    public static function distance(string $hash1, string $hash2): int
    {
        if (strlen($hash1) !== 16 || strlen($hash2) !== 16) {
            return 64;
        }

        $dist = 0;
        for ($i = 0; $i < 16; $i++) {
            $xor = hexdec($hash1[$i]) ^ hexdec($hash2[$i]);
            while ($xor) {
                $dist += $xor & 1;
                $xor >>= 1;
            }
        }

        return $dist;
    }

    /**
     * Get image dimensions. Cached in-memory.
     *
     * @return array{0: int, 1: int}|null [width, height]
     */
    public static function dimensions(string $imagePath): ?array
    {
        if (! file_exists($imagePath)) {
            return null;
        }

        try {
            $size = @getimagesize($imagePath);
            if ($size && $size[0] > 0 && $size[1] > 0) {
                return [(int) $size[0], (int) $size[1]];
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    /**
     * Check if two images have similar aspect ratio.
     * Used as a fast pre-filter before expensive color comparison.
     * Size is NOT checked — guide photos often appear as thumbnails
     * in the text, 20x smaller than the canonical.
     *
     * @param  float  $ratioTol  Max aspect ratio deviation (default 0.05 = ±5%)
     */
    public static function similarDimensions(
        int $w1,
        int $h1,
        int $w2,
        int $h2,
        float $ratioTol = 0.05
    ): bool {
        $ratio1 = $w1 / max($h1, 1);
        $ratio2 = $w2 / max($h2, 1);

        return abs($ratio1 - $ratio2) <= $ratioTol;
    }

    /**
     * Extract resized RGB pixel array from an image.
     *
     * @param  string  $imagePath  Filesystem path
     * @param  int  $targetW  Target width in pixels
     * @param  int  $targetH  Target height in pixels
     * @return array<int, array{0:int,1:int,2:int}>|null Flat [r,g,b] tuples
     */
    public static function extractPixels(
        string $imagePath,
        int $targetW,
        int $targetH
    ): ?array {
        if (! file_exists($imagePath)) {
            return null;
        }

        $fileSize = filesize($imagePath);
        if ($fileSize > 10 * 1024 * 1024) {
            return null;
        }

        try {
            $image = @imagecreatefromstring(file_get_contents($imagePath));
        } catch (\Throwable $e) {
            return null;
        }

        if (! $image) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if ($width < 1 || $height < 1) {
            imagedestroy($image);

            return null;
        }

        $resized = imagecreatetruecolor($targetW, $targetH);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetW, $targetH, $width, $height);
        imagedestroy($image);

        $pixels = [];
        for ($y = 0; $y < $targetH; $y++) {
            for ($x = 0; $x < $targetW; $x++) {
                $c = imagecolorat($resized, $x, $y);
                $pixels[] = [
                    ($c >> 16) & 0xFF,
                    ($c >> 8) & 0xFF,
                    $c & 0xFF,
                ];
            }
        }

        imagedestroy($resized);

        return $pixels;
    }

    /**
     * Pre-compute pixels for a set of canonical images.
     *
     * @param  array  $canonicals  Raw canonicals with 'path', 'tipo' keys
     * @param  int  $targetW  Target width for pixel extraction
     * @param  int  $targetH  Target height (calculated from AR for guides)
     * @return array Enriched with 'pixels', 'width', 'height', 'ratio'
     */
    public static function precomputeCanonicals(
        array $canonicals,
        int $targetW = 16,
        ?int $targetH = null
    ): array {
        $enriched = [];

        foreach ($canonicals as $c) {
            $sti = new StorageItem($c['path']);
            if (! $sti->exists()) {
                continue;
            }

            $dims = self::dimensions($sti->path);
            if ($dims === null) {
                continue;
            }

            [$w, $h] = $dims;

            $th = $targetH ?? (int) round($targetW * $h / $w);

            $pixels = self::extractPixels($sti->path, $targetW, $th);
            if ($pixels === null) {
                continue;
            }

            $enriched[] = array_merge($c, [
                'pixels' => $pixels,
                'width' => $w,
                'height' => $h,
                'ratio' => round($w / max($h, 1), 5),
                'grid_w' => $targetW,
                'grid_h' => $th,
            ]);
        }

        return $enriched;
    }

    /**
     * Compare a target image against pre-computed canonical pixel arrays,
     * with early termination.
     *
     * @param  string  $imagePath  Target image path
     * @param  array  $canonicals  Pre-computed canonicals with 'pixels' key
     * @param  float  $threshold  Min similarity % (e.g., 95.0)
     * @return array|null Best match + similarity
     */
    public static function findBestColorMatch(
        string $imagePath,
        array $canonicals,
        float $threshold = 95.0
    ): ?array {
        $bestSimilarity = 0.0;
        $bestMatch = null;

        foreach ($canonicals as $c) {
            if (empty($c['pixels']) || empty($c['grid_w']) || empty($c['grid_h'])) {
                continue;
            }

            $targetPixels = self::extractPixels($imagePath, $c['grid_w'], $c['grid_h']);
            if ($targetPixels === null) {
                continue;
            }

            $sim = self::comparePixelArrays($targetPixels, $c['pixels'], $threshold);

            if ($sim > $bestSimilarity) {
                $bestSimilarity = $sim;
                $bestMatch = $c;
            }

            if ($bestSimilarity >= 99.99) {
                break;
            }
        }

        if ($bestSimilarity >= $threshold && $bestMatch !== null) {
            return array_merge($bestMatch, [
                'similarity' => round($bestSimilarity, 2),
                'method' => 'color',
            ]);
        }

        return null;
    }

    /**
     * Compare two equally-sized pixel arrays with early termination.
     *
     * @param  array  $pixels1  Target pixel array [[r,g,b], ...]
     * @param  array  $pixels2  Canonical pixel array [[r,g,b], ...]
     * @param  float  $threshold  Min similarity %
     * @return float 0-100 similarity
     */
    private static function comparePixelArrays(array $pixels1, array $pixels2, float $threshold): float
    {
        $count = count($pixels1);
        if ($count !== count($pixels2) || $count === 0) {
            return 0.0;
        }

        $maxPerPixel = 3 * 255;
        $maxTotalDiff = $count * $maxPerPixel;
        $allowedDiff = (int) ((1.0 - $threshold / 100.0) * $maxTotalDiff);
        $diff = 0;

        for ($i = 0; $i < $count; $i++) {
            $diff += abs($pixels1[$i][0] - $pixels2[$i][0])
                   + abs($pixels1[$i][1] - $pixels2[$i][1])
                   + abs($pixels1[$i][2] - $pixels2[$i][2]);

            if ($diff > $allowedDiff) {
                return 0.0;
            }
        }

        return 100.0 - (($diff / $maxTotalDiff) * 100.0);
    }

    /**
     * Find best match via dHash (fast, structural similarity).
     */
    public static function findBestMatch(
        string $imagePath,
        array $canonicals,
        int $threshold = 3
    ): ?array {
        if (! file_exists($imagePath)) {
            return null;
        }

        $targetHash = self::hash($imagePath);
        if (empty($targetHash)) {
            return null;
        }

        $bestDistance = 65;
        $bestMatch = null;

        foreach ($canonicals as $canonical) {
            if (empty($canonical['hash'])) {
                continue;
            }

            $dist = self::distance($targetHash, $canonical['hash']);

            if ($dist < $bestDistance) {
                $bestDistance = $dist;
                $bestMatch = $canonical;
            }

            if ($dist === 0) {
                break;
            }
        }

        if ($bestDistance <= $threshold) {
            return array_merge($bestMatch, [
                'distance' => $bestDistance,
                'method' => 'dhash',
            ]);
        }

        return null;
    }
}
