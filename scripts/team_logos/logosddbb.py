import os

def generate_insert_statements(input_folder="team_logos"):
    """Genera las sentencias SQL para insertar las rutas de los logos en la base de datos."""
    insert_statements = []

    # Recorrer las carpetas de ligas
    for league_folder in os.listdir(input_folder):
        league_path = os.path.join(input_folder, league_folder)
        if not os.path.isdir(league_path):
            continue

        # Recorrer los logos de cada liga
        for logo_file in os.listdir(league_path):
            if not logo_file.endswith(".png"):  # Filtrar solo archivos .png
                continue

            team_name = logo_file.replace(".png", "")
            logo_path = os.path.join(league_path, logo_file)

            # Crear la sentencia SQL para insertar la ruta del logo
            sql_statement = f"""
            INSERT INTO `team_logos` (`team_name`, `league_name`, `logo_path`) 
            VALUES ('{team_name}', '{league_folder}', '{logo_path}');
            """
            insert_statements.append(sql_statement)

    return insert_statements

def save_sql_to_file(insert_statements, output_file="insert.sql"):
    """Guarda las sentencias SQL en un archivo .sql."""
    with open(output_file, "w", encoding="utf-8") as f:
        f.write("\n".join(insert_statements))
    print(f"✅ Sentencias SQL guardadas en: {output_file}")

if __name__ == "__main__":
    # Asegúrate de pasar la carpeta que contiene las subcarpetas de ligas
    insert_statements = generate_insert_statements(input_folder=".")  # Cambia la ruta si es necesario
    
    # Guardar las sentencias SQL en un archivo
    save_sql_to_file(insert_statements, "insert.sql")
