import pandas as pd
import mysql.connector
import os

# Download the CSV file
os.system("wget https://pvb4.fcict.nl/klanten.csv")

# Database configuration
config = {
    'user': 'kevinpvb4fciRQcj',
    'password': 'jEgz17C6nVNF4Rr2XplbYaLf',
    'host': '127.0.0.1',  # or 'localhost'
    'port': 3306,
    'database': 'kevin_pvb4_fcict_nl_KsgOMiDt'
}

try:
    # Establish a connection to the database
    conn = mysql.connector.connect(**config)

    # Create a cursor object
    cursor = conn.cursor()

    # Drop the existing table if it exists
    print("Dropping the existing table...")
    cursor.execute("DROP TABLE IF EXISTS your_table")
    print("Table dropped successfully.")

    # Read the CSV file into a DataFrame
    df = pd.read_csv('klanten.csv')

    # Ensure the columns in the CSV file match the database table columns
    expected_columns = {'Klant Id', 'Voornaam', 'Achternaam', 'Sekse', 'Emailadres', 'Geboortedatum'}
    if not expected_columns.issubset(df.columns):
        raise ValueError(f"CSV file is missing one or more required columns: {expected_columns}")

    # Create the table with an AUTO_INCREMENT id
    cursor.execute("""
        CREATE TABLE your_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            KlantID VARCHAR(15) UNIQUE,
            Voornaam VARCHAR(255),
            Achternaam VARCHAR(255),
            Sekse VARCHAR(10),
            Emailadres VARCHAR(255),
            geboortedatum DATE
        )
    """)
    print("Table created successfully.")

    # Iterate over each row in the DataFrame and insert into the database
    for index, row in df.iterrows():
        cursor.execute(
            "INSERT INTO your_table (KlantID, Voornaam, Achternaam, Sekse, Emailadres, geboortedatum) VALUES (%s, %s, %s, %s, %s, %s)",
            (str(row['Klant Id'])[:15], row['Voornaam'], row['Achternaam'], str(row['Sekse'])[:10], row['Emailadres'], row['Geboortedatum'])
        )

    # Commit the transaction
    conn.commit()

    # Close the cursor and connection
    cursor.close()
    conn.close()

    print("Data imported successfully.")

except mysql.connector.Error as e:
    print(f"Error connecting to MySQL: {e}")

except ValueError as ve:
    print(f"Value error: {ve}")

except Exception as ex:
    print(f"An error occurred: {ex}")
