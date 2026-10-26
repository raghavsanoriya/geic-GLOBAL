"""Browser smoke coverage for the Expo web renderer.

Run Laravel on port 8086 and Expo web on port 8083 before this script.
"""

from pathlib import Path

from playwright.sync_api import sync_playwright


BASE_URL = "http://127.0.0.1:8083"
SCREENSHOT_DIR = Path("C:/Users/sarfa/AppData/Local/Temp")


def assert_no_horizontal_overflow(page):
    assert page.evaluate("document.body.scrollWidth <= document.documentElement.clientWidth")


def screenshot(page, name):
    page.screenshot(path=str(SCREENSHOT_DIR / f"geic-mobile-{name}.png"), full_page=True)


with sync_playwright() as playwright:
    browser = playwright.chromium.launch(headless=True)
    browser_errors = []
    for width, height in [(320, 812), (375, 812), (414, 896), (1024, 768)]:
        page = browser.new_page(viewport={"width": width, "height": height})
        page.on("pageerror", lambda error: browser_errors.append(str(error)))
        page.on(
            "console",
            lambda message: browser_errors.append(message.text)
            if message.type in ["error", "warning"]
            else None,
        )
        page.goto(BASE_URL)
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(500)
        assert_no_horizontal_overflow(page)
        assert page.get_by_text("Make your next decision easier").is_visible()
        for tab in ["Home", "Explore", "AI Advisor", "Evaluate", "More"]:
            assert page.get_by_label(f"{tab} tab").is_visible()
        page.close()

    page = browser.new_page(viewport={"width": 375, "height": 812})
    page.on("pageerror", lambda error: browser_errors.append(str(error)))
    page.on(
        "console",
        lambda message: browser_errors.append(message.text)
        if message.type in ["error", "warning"]
        else None,
    )
    page.goto(BASE_URL)
    page.wait_for_load_state("networkidle")
    screenshot(page, "home")

    page.get_by_label("Explore tab").click()
    page.get_by_label("Search destinations").fill("Australia")
    australia = page.get_by_role("button", name="Open Australia destination", exact=True)
    assert australia.is_visible()
    australia.click()
    assert page.get_by_text("Plan with verified guidance").is_visible()
    page.get_by_label("Go back").click()
    screenshot(page, "explore")

    page.get_by_label("AI Advisor tab").click()
    assert page.get_by_role("heading", name="Ask your next question", exact=True).is_visible()
    assert page.get_by_label("Message the AI Advisor").is_visible()
    screenshot(page, "advisor")

    page.get_by_label("Evaluate tab").click()
    page.get_by_role("button", name="Evaluate my profile").click()
    assert page.get_by_text("Choose at least one destination.").is_visible()
    page.get_by_role("button", name="Australia").click()
    page.get_by_role("button", name="Evaluate my profile").click()
    page.get_by_text("Your planning readiness").wait_for(state="visible", timeout=15000)
    page.get_by_role("checkbox", name="Valid passport").click()
    assert page.get_by_text("1 of 6 ready").is_visible()
    screenshot(page, "evaluate")

    page.get_by_label("More tab").click()
    assert page.get_by_text("Everything for your journey").is_visible()
    page.get_by_label("Open Expert Counselling").click()
    assert page.get_by_text("What this helps you achieve").is_visible()
    page.get_by_role("button", name="Start my plan").click()
    page.get_by_role("button", name="Submit counselling request").click()
    assert page.get_by_text("Please check the highlighted fields.").is_visible()
    screenshot(page, "booking")

    browser.close()

    ignored = [
        error for error in browser_errors
        if "favicon" not in error.lower()
        and '"shadow*" style props are deprecated' not in error
        and "props.pointerEvents is deprecated" not in error
    ]
    assert not ignored, "\n".join(ignored[:10])

print("GEIC Expo UI checks passed for phone, tablet, landscape, navigation and forms.")
