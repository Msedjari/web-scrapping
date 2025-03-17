import os
import json

def escape_string(value):
    """Escapa comillas simples y dobles en valores de texto para evitar errores en SQL."""
    if value is None:
        return "NULL"
    if isinstance(value, str):
        value = value.replace("'", "\\'").replace('"', '\\"')
        return f"'{value}'"
    return str(value)

def sanitize_int(value):
    """Convierte valores en enteros si son válidos, o devuelve NULL."""
    try:
        return int(value)
    except (ValueError, TypeError):
        return "NULL"

def extract_match_data_from_json(file_path):
    """Lee un archivo JSON y extrae los datos del partido, manejando JSONL o listas de JSON."""
    match_data_list = []

    try:
        with open(file_path, 'r', encoding='utf-8') as file:
            first_char = file.read(1)  # Leer primer carácter del archivo
            file.seek(0)  # Volver al inicio del archivo
            
            if first_char == "[":  
                # El archivo contiene una lista de objetos JSON
                data = json.load(file)
                if isinstance(data, list):
                    match_data_list.extend(data)
                else:
                    print(f"⚠️ Advertencia: El archivo {file_path} no contiene una lista válida.")
            else:
                # El archivo tiene múltiples objetos JSON en líneas separadas (JSONL)
                for line in file:
                    try:
                        match_data = json.loads(line.strip())
                        if isinstance(match_data, dict):
                            match_data_list.append(match_data)
                        else:
                            print(f"⚠️ Advertencia: Línea ignorada en {file_path} (no es un diccionario válido).")
                    except json.JSONDecodeError:
                        print(f"❌ Error de formato en línea de {file_path}. Línea ignorada.")
    except Exception as e:
        print(f"❌ Error al leer {file_path}: {e}")
    
    return match_data_list

def generate_insert_statements(match_data_list):
    """Genera sentencias SQL INSERT para la tabla match_data."""
    sql_statements = []
    
    for match_data in match_data_list:
        if isinstance(match_data, str):
            print(f"⚠️ Advertencia: Se esperaba un diccionario, pero se recibió un string. Ignorando: {match_data}")
            continue

        sql = f"""
        INSERT INTO match_data (
            url, team_home, team_away, score_home, score_away, match_time
        ) VALUES (
            {escape_string(match_data.get('url'))}, {escape_string(match_data.get('team_home'))},
            {escape_string(match_data.get('team_away'))}, {sanitize_int(match_data.get('score_home'))},
            {sanitize_int(match_data.get('score_away'))}, {escape_string(match_data.get('match_time'))}
        );
        """
        sql_statements.append(sql)
    
    return sql_statements

def save_sql_to_file(sql_statements, file_path):
    """Guarda las sentencias SQL en un archivo."""
    with open(file_path, "w", encoding='utf-8') as f:
        f.write("\n".join(sql_statements))
    print(f"✅ SQL guardado en: {file_path}")

if __name__ == "__main__":
    directory = 'match_links'  # Carpeta con los archivos JSON
    output_sql_path = os.path.join(directory, "insert.sql")
    
    match_data_list = []

    for filename in os.listdir(directory):
        if filename.endswith(".json"):
            input_json_path = os.path.join(directory, filename)
            match_data_list.extend(extract_match_data_from_json(input_json_path))
    
    if match_data_list:
        sql_statements = generate_insert_statements(match_data_list)
        save_sql_to_file(sql_statements, output_sql_path)
    else:
        print("⚠️ No se encontraron datos válidos en los archivos JSON.")
