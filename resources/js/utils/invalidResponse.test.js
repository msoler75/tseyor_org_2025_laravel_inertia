import { describe, it, expect, vi } from "vitest";
import {
  resolveInvalidTarget,
  handleInvalidResponse,
} from "./invalidResponse.js";

const CURRENT = "https://tseyor.test/miembros";

function fakeEvent(response, { prevented = false } = {}) {
  return {
    defaultPrevented: prevented,
    preventDefault() {
      this.defaultPrevented = true;
    },
    detail: response === undefined ? undefined : { response },
  };
}

function fakeResponse({ url, method = "get", prefetch = false } = {}) {
  return {
    requestParams: {
      all: () => ({ url, method, prefetch }),
      isPrefetch: () => prefetch,
    },
  };
}

describe("resolveInvalidTarget", () => {
  it("GET same-origin: returns the visit URL", () => {
    expect(
      resolveInvalidTarget(
        fakeResponse({ url: "https://tseyor.test/archivos/equipos/interiorizacion" }),
        CURRENT
      )
    ).toBe("https://tseyor.test/archivos/equipos/interiorizacion");
  });

  it("relative visit URL is resolved against the current origin", () => {
    expect(
      resolveInvalidTarget(fakeResponse({ url: "/archivos/faltante" }), CURRENT)
    ).toBe("https://tseyor.test/archivos/faltante");
  });

  it("POST: falls back to the current page (never re-submit)", () => {
    expect(
      resolveInvalidTarget(
        fakeResponse({ url: "https://tseyor.test/inscripciones", method: "post" }),
        CURRENT
      )
    ).toBe(CURRENT);
  });

  it("cross-origin visit URL is ignored for safety", () => {
    expect(
      resolveInvalidTarget(
        fakeResponse({ url: "https://evil.test/archivos" }),
        CURRENT
      )
    ).toBe(CURRENT);
  });

  it("missing response data falls back to the current page", () => {
    expect(resolveInvalidTarget(undefined, CURRENT)).toBe(CURRENT);
    expect(resolveInvalidTarget({}, CURRENT)).toBe(CURRENT);
    expect(
      resolveInvalidTarget(fakeResponse({ method: "put" }), CURRENT)
    ).toBe(CURRENT);
  });
});

describe("handleInvalidResponse", () => {
  it("cancels Inertia's error modal and navigates to the visit URL", () => {
    const assign = vi.fn();
    const event = fakeEvent(
      fakeResponse({ url: "https://tseyor.test/archivos/faltante" })
    );

    handleInvalidResponse(event, { href: CURRENT, assign });

    expect(event.defaultPrevented).toBe(true);
    expect(assign).toHaveBeenCalledExactlyOnceWith(
      "https://tseyor.test/archivos/faltante"
    );
  });

  it("cancels the modal but does not navigate on prefetch responses", () => {
    const assign = vi.fn();
    const event = fakeEvent(
      fakeResponse({ url: "https://tseyor.test/archivos/faltante", prefetch: true })
    );

    handleInvalidResponse(event, { href: CURRENT, assign });

    expect(event.defaultPrevented).toBe(true);
    expect(assign).not.toHaveBeenCalled();
  });

  it("never throws on a malformed event", () => {
    const assign = vi.fn();

    handleInvalidResponse(fakeEvent(undefined), { href: CURRENT, assign });
    handleInvalidResponse({}, { href: CURRENT, assign });
    handleInvalidResponse(undefined, { href: CURRENT, assign });

    expect(assign).toHaveBeenCalledWith(CURRENT);
  });
});
