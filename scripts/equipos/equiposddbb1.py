import os
import json

def load_leagues_data(directory_path):
    # (Mantener la función sin cambios)
    ...

def sanitize_int(value):
    # (Mantener la función sin cambios)
    ...

def escape_string(value):
    """
    Escapa las comillas simples y otros caracteres especiales en los valores de texto.
    """
    if value is None:
        return "NULL"  # Si el valor es None, se insertará como NULL

    if isinstance(value, str):
        # Escapamos las comillas simples y otros caracteres especiales
        value = value.replace("'", "\\'")  # Escapa las comillas simples
        value = value.replace('"', '\\"')   # Escapa las comillas dobles
        # Asegúrate de que el valor esté entre comillas simples
        return f"'{value}'"

    return str(value)  # Para otros tipos de datos (números, etc.), lo convertimos a cadena

def generate_insert_statements(leagues_data):
    sql_statements = []
    existing_clubs = set()  # Nuevo conjunto para rastrear clubes únicos
    
    for league, data in leagues_data.items():
        for team_data in data:
            if not isinstance(team_data, dict):
                print(f"❌ Error: El equipo en la liga '{league}' no es un diccionario.")
                continue

            club_name = team_data.get('club_name', '').strip()  # Limpiar espacios
            if not club_name:
                print(f"⚠️ Advertencia: Club sin nombre en la liga '{league}'. Omitiendo.")
                continue

            # Verificar si el club ya existe
            if club_name in existing_clubs:
                print(f"⚠️ Duplicado: '{club_name}' ya existe. Omitiendo.")
                continue
            existing_clubs.add(club_name)  # Registrar club

            team_info = {
                "league": league,
                "club_name": club_name,
                "president": team_data.get('president', ''),
                "coach": team_data.get('coach', ''),
                "foundation_year": sanitize_int(team_data.get('foundation_year')),
                "website": team_data.get('website', ''),
                "stadium_name": team_data.get('stadium', {}).get('name', ''),
                "stadium_capacity": sanitize_int(team_data.get('stadium', {}).get('capacity')),
                "stadium_address": team_data.get('stadium', {}).get('address', ''),
                "stadium_image": team_data.get('stadium', {}).get('image', ''),
                "escudo_image": team_data.get('images', {}).get('escudo', ''),
                "plantilla_image": team_data.get('images', {}).get('plantilla', ''),
                "url": team_data.get('url', '')
            }

            sql = f"""
            INSERT INTO team_info (
                league, club_name, president, coach, foundation_year, website,
                stadium_name, stadium_capacity, stadium_address, stadium_image,
                escudo_image, plantilla_image, url
            ) VALUES (
                {escape_string(team_info['league'])}, {escape_string(team_info['club_name'])}, {escape_string(team_info['president'])},
                {escape_string(team_info['coach'])}, {escape_string(team_info['foundation_year'])}, {escape_string(team_info['website'])},
                {escape_string(team_info['stadium_name'])}, {escape_string(team_info['stadium_capacity'])}, {escape_string(team_info['stadium_address'])},
                {escape_string(team_info['stadium_image'])}, {escape_string(team_info['escudo_image'])}, {escape_string(team_info['plantilla_image'])},
                {escape_string(team_info['url'])}
            );
            """
            sql_statements.append(sql)
    
    return sql_statements

# Las funciones write_to_sql_file y main permanecen iguales

