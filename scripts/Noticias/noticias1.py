import os
import json
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def scrape_all_news(news_url, output_file):
    options = Options()
    options.headless = True
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--disable-gpu')
    options.add_argument('--window-size=1920,1080')

    driver = webdriver.Chrome(options=options)

    try:
        driver.get(news_url)
        print(f"\nAccessing: {news_url}")

        try:
            # Wait until news articles are visible
            WebDriverWait(driver, 10).until(
                EC.presence_of_all_elements_located((By.CSS_SELECTOR, "div[data-testid='wcl-elementBodyNews'] a.wcl-article_Qco4e"))
            )

            news_items = driver.find_elements(By.CSS_SELECTOR, "div[data-testid='wcl-elementBodyNews'] a.wcl-article_Qco4e")

            news_data = []

            for item in news_items:
                try:
                    # Extract the link to the news article
                    link = item.get_attribute("href")

                    # Extract the title
                    title = item.find_element(By.CSS_SELECTOR, "h6.wcl-news-heading-08_bNlM-").text

                    # Extract the text/content (if accessible)
                    text = item.text

                    news_data.append({
                        "link": link,
                        "title": title,
                        "text": text
                    })

                except Exception as e:
                    print(f"Error processing a news item: {e}")

            print(f"Extracted news: {len(news_data)}")

            # Save the extracted data to a JSON file
            with open(output_file, "w", encoding="utf-8") as f:
                json.dump(news_data, f, ensure_ascii=False, indent=4)

        except Exception as e:
            print(f"Error extracting data from {news_url}: {e}")

    finally:
        driver.quit()

def scrape_article_content(input_file, output_file):
    options = Options()
    options.headless = True
    options.add_argument('--no-sandbox')
    options.add_argument('--disable-dev-shm-usage')
    options.add_argument('--disable-gpu')
    options.add_argument('--window-size=1920,1080')

    driver = webdriver.Chrome(options=options)

    try:
        # Load the news data
        with open(input_file, "r", encoding="utf-8") as f:
            news_data = json.load(f)

        for news_item in news_data:
            try:
                link = news_item.get("link")
                driver.get(link)
                print(f"Accessing article: {link}")

                # Wait until the article content is visible
                WebDriverWait(driver, 10).until(
                    EC.presence_of_element_located((By.CSS_SELECTOR, "div.fsNewsArticle__content"))
                )

                # Extract the article text
                article_content = driver.find_element(By.CSS_SELECTOR, "div.fsNewsArticle__content").text
                news_item["content"] = article_content

            except Exception as e:
                print(f"Error extracting content from {link}: {e}")

        # Save the updated news data with content to a new JSON file
        with open(output_file, "w", encoding="utf-8") as f:
            json.dump(news_data, f, ensure_ascii=False, indent=4)

    finally:
        driver.quit()


if __name__ == "__main__":
    news_url = "https://www.flashscore.es/noticias/futbol/"
    output_file = "news_data.json"

    # Run the scraper function
    scrape_all_news(news_url, output_file)

    print(f"Data has been saved to {output_file}")

    # Extract detailed content from each article
    detailed_output_file = "detailed_news_data.json"
    scrape_article_content(output_file, detailed_output_file)

    print(f"Detailed data has been saved to {detailed_output_file}")
