import { describe, it, expect } from "vitest";
import {
  HtmlToMarkdown,
  MarkdownToHtml,
  DecodeHtml,
  detectFormat,
} from "./markdown.js";

// Convierte el wrapper <html><head></head><body>…</body></html> que produce
// _toHtmlTables en el innerHTML del body para aserciones concisas.
function body(html) {
  const doc = new DOMParser().parseFromString(html, "text/html");
  return doc.body.innerHTML;
}

describe("reglas custom html -> markdown (turndown)", () => {
  it("imagen con width/height", () => {
    expect(
      HtmlToMarkdown(
        "<p><img src='x.jpg' alt='alt' width='100%' height='50px'></p>"
      )
    ).toBe("![alt](x.jpg){width=100%,height=50px}");
  });

  it("imagen con width/height via style (sobreescribe)", () => {
    expect(
      HtmlToMarkdown(
        "<p><img src='x.jpg' alt='alt' width='100' style='width:200px; height:90px'></p>"
      )
    ).toBe("![alt](x.jpg){width=200px,height=90px}");
  });

  it("imagen sin atributos (sin sufijo)", () => {
    expect(HtmlToMarkdown("<p><img src='x.jpg' alt='alt'></p>")).toBe(
      "![alt](x.jpg)"
    );
  });

  it("párrafo con style", () => {
    expect(
      HtmlToMarkdown("<p style='text-align: center'>Hola</p>")
    ).toBe("{style=text-align: center}Hola");
  });

  it("párrafo con text-align:left se descarta", () => {
    expect(
      HtmlToMarkdown("<p style='text-align:left'>Hola</p>")
    ).toBe("Hola");
  });
});

describe("MarkdownToHtml (showdown): reglas custom >= html", () => {
  it("aplica {width=…,height=…} al <img>", () => {
    const h = body(MarkdownToHtml("![alt](x.jpg){width=200px,height=90px}"));
    expect(h).toContain("<img");
    expect(h).toContain('width="200px"');
    expect(h).toContain('height="90px"');
    expect(h).toContain('src="x.jpg"');
    expect(h).not.toContain("{width=");
  });

  it("aplica {style=…} al <p>", () => {
    const h = body(MarkdownToHtml("{style=text-align: center}Intro"));
    expect(h).toContain('style="text-align: center"');
    expect(h).toContain("Intro");
    expect(h).not.toContain("{style=");
  });

  it("center de imagen solitaria", () => {
    const h = body(MarkdownToHtml("![alt](x.jpg)"));
    expect(h).toContain('style="text-align: center"');
    expect(h).toContain('<img src="x.jpg"');
  });
});

describe("round-trip", () => {
  it("párrafo + imagen con dimensiones conserva contenido", () => {
    const md = HtmlToMarkdown(
      "<p style='text-align: right'>Intro <strong>negrita</strong></p><p><img src='foto.jpg' alt='alt' width='300px' height='200px'></p>"
    );
    const h = body(MarkdownToHtml(md));
    expect(h).toContain('style="text-align: right"');
    expect(h).toContain("<strong>negrita</strong>");
    expect(h).toContain('width="300px"');
    expect(h).toContain('height="200px"');
    expect(h).not.toContain("{style=");
    expect(h).not.toContain("{width=");
  });
});

describe("detectFormat & DecodeHtml", () => {
  it("detecta html", () => {
    expect(detectFormat("<p>hola</p>")).toMatchObject({ format: "html" });
  });
  it("detecta markdown", () => {
    expect(detectFormat("## Título\n**negrita**")).toMatchObject({
      format: "md",
    });
  });
  it("decode entity < >", () => {
    expect(DecodeHtml("a &lt;b&gt;")).toBe("a <b>");
  });
});