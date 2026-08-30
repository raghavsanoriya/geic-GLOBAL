"""Playwright interaction and verification smoke checks for the public UI."""

from playwright.sync_api import Page, sync_playwright

BASE_URL = "http://127.0.0.1:8000"
PATHS = ["/", "/compare-destinations", "/emi-calculator", "/education-loans", "/ai-agents", "/contact"]


def visit(page: Page, path: str) -> None:
    response = page.goto(f"{BASE_URL}{path}", wait_until="networkidle")
    assert response is not None and response.ok, path


def verify_no_overflow(page: Page) -> None:
    size = page.evaluate(
        """() => {
            window.scrollTo(10000, 0);
            return {
                width: innerWidth,
                clientWidth: document.documentElement.clientWidth,
                scrollX: window.scrollX,
                bodyRight: document.body.getBoundingClientRect().right,
            };
        }"""
    )
    assert size["scrollX"] == 0, size
    assert size["bodyRight"] <= size["width"] + 1, size


def main() -> None:
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True)
        page = browser.new_page()
        for width, height in ((1440, 900), (375, 812)):
            page.set_viewport_size({"width": width, "height": height})
            for path in PATHS:
                visit(page, path)
                verify_no_overflow(page)

        page.set_viewport_size({"width": 1440, "height": 900})
        visit(page, "/ai-agents")
        cards = page.locator("[data-agent-card]")
        assert cards.count() == 6
        page.locator("[data-agent-search]").fill("travel")
        assert cards.locator(":visible").count() == 1
        assert page.locator("[data-agent-empty]").is_hidden()

        visit(page, "/emi-calculator")
        emi = page.locator("[data-emi-value]")
        before = emi.text_content()
        page.locator("#emi-principal").fill("3000000")
        page.wait_for_timeout(100)
        assert emi.text_content() != before
        assert page.locator("[data-emi-schedule] tr").count() == 10
        assert page.locator("[data-emi-chart] [data-emi-bars] > *").count() == 10

        visit(page, "/")
        page.locator('a[href="/ai-agents"]').first.click()
        page.wait_for_load_state("networkidle")
        assert page.url.rstrip("/").endswith("/ai-agents")
        browser.close()


if __name__ == "__main__":
    main()
