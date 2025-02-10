import json
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def setup_driver():
    options = Options()
    options.add_argument("--headless")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--disable-gpu")
    options.add_argument("--window-size=1920,1080")
    options.add_argument("--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36")
    return webdriver.Chrome(options=options)

def scrape_team_links(urls, output_file="info_teams.json"):
    driver = setup_driver()
    leagues = {
        "https://www.lne.es/deportes/futbol/liga-alemana/equipos.html": "Bundesliga",
        "https://www.lne.es/deportes/futbol/primera-division/equipos.html": "LaLiga",
        "https://www.lne.es/deportes/futbol/liga-francesa/equipos.html": "Ligue 1",
        "https://www.lne.es/deportes/futbol/calcio-serie-a/equipos.html": "Serie A",
        "https://www.lne.es/deportes/futbol/premier-league/equipos.html" : "Premier League"
    }
    teams_data = {}

    try:
        for url in urls:
            driver.get(url)
            league_name = leagues.get(url, "Unknown League")
            WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.CLASS_NAME, "equipoescudo")))

            # Get all team links
            team_elements = driver.find_elements(By.CSS_SELECTOR, ".equipoescudo a.geca_enlace_equipo")
            team_links = [elem.get_attribute("href") for elem in team_elements]

            # Store the team links under the league name
            teams_data[league_name] = team_links

        # Save links to a JSON file
        with open(output_file, "w", encoding="utf-8") as f:
            json.dump(teams_data, f, indent=4, ensure_ascii=False)

        print(f"Team links saved to {output_file}")

    except Exception as e:
        print(f"Error scraping team links: {e}")

    finally:
        driver.quit()

if __name__ == "__main__":
    urls = [
        "https://www.lne.es/deportes/futbol/liga-alemana/equipos.html",
        "https://www.lne.es/deportes/futbol/primera-division/equipos.html",
        "https://www.lne.es/deportes/futbol/liga-francesa/equipos.html",
        "https://www.lne.es/deportes/futbol/calcio-serie-a/equipos.html",
        "https://www.lne.es/deportes/futbol/premier-league/equipos.html"
    ]
    scrape_team_links(urls)
