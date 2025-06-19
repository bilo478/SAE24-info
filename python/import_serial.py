import serial
import mysql.connector
import requests
import time
from db_config import DB_CONFIG

# Port série
PORT_SERIE = 'COM4'
VITESSE_BAUD = 9600

# Connexion à la base de données
db = mysql.connector.connect(**DB_CONFIG)
curseur = db.cursor()

# Port série
ser = serial.Serial(PORT_SERIE, VITESSE_BAUD, timeout=1)
print(f"Lecture en cours sur {PORT_SERIE}...")

dernier_message = None

try:
    while True:
        ligne = ser.readline().decode('utf-8').strip()

        # Insertion si nouveau message
        if ligne in ['>>> Présence détectée !', 'Aucune présence.'] and ligne != dernier_message:
            print(f">> Nouveau : {ligne}")
            curseur.execute("INSERT INTO mesures (valeur) VALUES (%s)", (ligne,))
            db.commit()
            dernier_message = ligne

        time.sleep(0.1)

except KeyboardInterrupt:
    print("Arrêté manuellement.")
    ser.close()
    db.close()
