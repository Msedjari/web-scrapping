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

def scrape_team_info(input_file="info_teams.json"):
    driver = setup_driver()

    # Cargar los enlaces desde el archivo JSON
    with open(input_file, "r", encoding="utf-8") as f:
        leagues_data = json.load(f)

    # Diccionario para almacenar la información detallada
    detailed_teams_data = {}

    try:
        for league, team_links in leagues_data.items():
            detailed_teams_data[league] = []

            for team_url in team_links:
                try:
                    driver.get(team_url)
                    WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.CLASS_NAME, "datos")))

                    # 📌 Extraer información del club
                    club_name = driver.find_element(By.CSS_SELECTOR, ".datos-info__img").text.strip()
                    president = driver.find_element(By.XPATH, "//li[contains(text(),'Presidente')]/strong").text.strip()
                    coach = driver.find_element(By.XPATH, "//li[contains(text(),'Entrenador')]/strong").text.strip()
                    foundation_year = driver.find_element(By.XPATH, "//li[contains(text(),'Año de fundación')]/strong").text.strip()
                    website = driver.find_element(By.XPATH, "//li[contains(text(),'Web')]/a").get_attribute("href")

                    # 📌 Extraer información del estadio
                    stadium_name = driver.find_element(By.CSS_SELECTOR, ".estadio__titulo h3").text.strip()
                    capacity = driver.find_element(By.XPATH, "//li[contains(text(),'Aforo')]/strong").text.strip()
                    address = driver.find_element(By.XPATH, "//li[contains(text(),'Dirección')]/strong").text.strip()

                    # 📌 Extraer imágenes
                    escudo_img = driver.find_element(By.CSS_SELECTOR, ".datos-info__img img").get_attribute("src")
                    estadio_img = driver.find_element(By.CSS_SELECTOR, ".estadio-info__img img").get_attribute("src")
                    plantilla_img = driver.find_element(By.CSS_SELECTOR, "img.foto_plantilla").get_attribute("src")

                    # 📌 Guardar la información
                    team_info = {
                        "club_name": club_name,
                        "president": president,
                        "coach": coach,
                        "foundation_year": foundation_year,
                        "website": website,
                        "stadium": {
                            "name": stadium_name,
                            "capacity": capacity,
                            "address": address,
                            "image": estadio_img
                        },
                        "images": {
                            "escudo": escudo_img,
                            "plantilla": plantilla_img
                        },
                        "url": team_url
                    }
                    
                    detailed_teams_data[league].append(team_info)

                    print(f"✅ Scraped info for {club_name}")

                except Exception as e:
                    print(f"❌ Error scraping team {team_url}: {e}")
                    continue

            # 📌 Guardar cada liga en un archivo JSON separado
            output_file = f"{league.replace(' ', '_')}.json"
            with open(output_file, "w", encoding="utf-8") as f:
                json.dump(detailed_teams_data[league], f, indent=4, ensure_ascii=False)

            print(f"📁 Data for {league} saved to {output_file}")

    except Exception as e:
        print(f"⚠️ Error processing team info: {e}")

    finally:
        driver.quit()

if __name__ == "__main__":
    scrape_team_info()
