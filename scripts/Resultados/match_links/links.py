import os
import json
from selenium import webdriver  # type: ignore
from selenium.webdriver.common.by import By  # type: ignore
from selenium.webdriver.chrome.options import Options
import time

def get_match_links_from_urls(urls, output_folder="match_links"):
    # Configuración del navegador para Selenium
    options = Options()
    options.headless = False  # Cambia a True para modo headless
    driver = webdriver.Chrome(options=options)

    # Crear la carpeta de salida si no existe
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    try:
        for url in urls:
            print(f"\nAccediendo a: {url}")
            driver.get(url)
            time.sleep(5)  # Espera para permitir la carga completa de la página

            # Identificar los enlaces de los partidos en la página actual
            match_links_elements = driver.find_elements(By.CSS_SELECTOR, "a.eventRowLink")
            match_links = [link.get_attribute("href") for link in match_links_elements]

            # Extraer el nombre de la liga o torneo de la URL
            league_name = url.split('/')[-3] if url.split('/')[-1] == '' else url.split('/')[-2]

            # Guardar los enlaces en un archivo JSON
            output_file = os.path.join(output_folder, f"{league_name}_matches.json")
            with open(output_file, "w") as f:
                json.dump(match_links, f, indent=4)

            print(f"Encontrados {len(match_links)} partidos en {url}")
            print(f"Enlaces guardados en {output_file}")
    finally:
        # Cerramos el navegador
        driver.quit()

if __name__ == "__main__":
    # Lista de URLs para las diferentes ligas
    urls = [
        "https://www.flashscore.com/football/spain/laliga/results/",
        "https://www.flashscore.com/football/europe/champions-league/results/",
        "https://www.flashscore.com/football/england/premier-league/results/",
        "https://www.flashscore.com/football/germany/bundesliga/results/",
        "https://www.flashscore.com/football/italy/serie-a/results/",
    ]

    # Extraemos los enlaces de los partidos y guardamos en archivos JSON
    get_match_links_from_urls(urls)
