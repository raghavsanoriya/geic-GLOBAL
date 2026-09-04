"""Local smoke coverage for the Expo web renderer.

Run with the Metro web server already running on port 8083:
    python e2e/local-ui-check.py
"""

from pathlib import Path
from playwright.sync_api import sync_playwright


BASE_URL = "http://127.0.0.1:8083"
SCREENSHOT_DIR = Path("C:/Users/sarfa/AppData/Local/Temp")


def assert_no_horizontal_overflow(page):
    assert page.evaluate("document.body.scrollWidth <= document.documentElement.clientWidth")


def screenshot(page, name):
    page.screenshot(path=str(SCREENSHOT_DIR / f"transglobe-web-{name}.png"), full_page=True)


with sync_playwright() as playwright:
    browser = playwright.chromium.launch(headless=True)
    for width, height in [(320, 812), (375, 812), (414, 896)]:
        page = browser.new_page(viewport={"width": width, "height": height})
        page.goto(BASE_URL)
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(500)
        assert_no_horizontal_overflow(page)
        assert page.get_by_text("Good morning, Sarfaraz").is_visible()
        assert page.get_by_role("button", name="Compare destinations").is_visible()
        assert page.get_by_role("tab", name="Home tab").is_visible()
        for tab in ["Explore", "Events", "Services", "Apply"]:
            assert page.get_by_role("tab", name=f"{tab} tab").is_visible()
        page.close()

    page = browser.new_page(viewport={"width": 375, "height": 812})
    page.goto(BASE_URL)
    page.wait_for_load_state("networkidle")
    page.wait_for_timeout(500)
    screenshot(page, "home")

    # Quick actions open native detail routes, and details return to the source tab.
    page.get_by_role("button", name="EMI calculator").click()
    assert page.get_by_text("Service guide").is_visible()
    assert page.get_by_text("Loans and funding").is_visible()
    page.get_by_role("button", name="Go back").click()
    assert page.get_by_text("Jump back in").is_visible()

    # Explore search and empty state.
    page.get_by_role("tab", name="Explore tab").click()
    assert page.get_by_text("Find your place").is_visible()
    page.get_by_label("Search destinations, tools or guides").fill("canada")
    assert page.get_by_role("button", name="Open Canada destination").count() == 1
    assert page.get_by_text("Canada", exact=True).count() >= 2
    page.get_by_label("Search destinations, tools or guides").fill("zzzz")
    assert page.get_by_text("No matches yet").is_visible()
    page.get_by_role("button", name="Clear destination search").click()
    assert page.get_by_text("Destinations for you").is_visible()
    screenshot(page, "explore")

    # Events detail and local registration feedback.
    page.get_by_role("tab", name="Events tab").click()
    assert page.get_by_text("Events that move").is_visible()
    page.get_by_role("button", name="Open Global Uni Expo").click()
    page.get_by_role("button", name="Reserve my place").click()
    assert page.get_by_text("Your place is reserved locally").is_visible()
    page.get_by_role("button", name="Go back").click()
    screenshot(page, "events")

    # Service detail can send the user to the focused application form.
    page.get_by_role("tab", name="Services tab").click()
    assert page.get_by_text("Support for your").is_visible()
    page.get_by_role("button", name="Open University admissions").click()
    assert page.get_by_text("Service guide").is_visible()
    page.get_by_role("button", name="Start my plan").click()
    assert page.get_by_text("Book free counselling").is_visible()
    screenshot(page, "services")

    # Apply form labels, validation, and success state.
    page.get_by_role("tab", name="Apply tab").click()
    page.get_by_role("button", name="Submit counselling request").click()
    assert page.get_by_text("Please complete every field").is_visible()
    values = {
        "Your name": "Sarfaraz Khan",
        "Email address": "sarfaraz@example.com",
        "Phone number": "+91 99999 99999",
        "Preferred destination": "Canada",
        "Study level": "Master's",
        "Target intake": "September 2026",
    }
    for label, value in values.items():
        page.get_by_label(label).fill(value)
    page.get_by_role("button", name="Submit counselling request").click()
    assert page.get_by_text("Your counselling request is ready").is_visible()
    assert page.get_by_text("Thanks, Sarfaraz.").is_visible()
    screenshot(page, "apply")

    browser.close()

print("Local Expo UI checks passed at 320px, 375px, and 414px.")
