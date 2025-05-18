// tests/e2e/portada.spec.ts
import { test, expect } from '@playwright/test';

test.describe('Portada', () => {
  test('La portada carga correctamente y muestra los elementos principales', async ({ page }) => {
    await page.goto('/');
    // Espera a que el título principal esté presente
    await expect(page.locator('h1')).toHaveCount(1);
    // Verifica que algunos textos clave estén presentes
    await expect(page.locator('body')).toContainText([/Contacto Extraterrestre|Bienvenid@ a Mundo Armónico Tseyor/]);
    // Verifica que el botón "Quiénes somos" esté visible (puede ser <a> o <button>)
    const quienBtn = page.getByRole('button', { name: /Quiénes somos/i });
    const quienLink = page.getByRole('link', { name: /Quiénes somos/i });
    const btnVisible = await quienBtn.isVisible().catch(() => false);
    const linkVisible = await quienLink.isVisible().catch(() => false);
    expect(btnVisible || linkVisible).toBe(true);
    // Verifica que haya al menos una sección Hero
    expect(await page.locator('.Hero').count()).toBeGreaterThan(0);
  });
});
