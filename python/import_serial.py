import serial
import mysql.connector
import time
from db_config import DB_CONFIG  # ← on importe depuis ton fichier

# Port série
PORT_SERIE = 'COM4'
VITESSE_BAUD = 9600

# Connexion MySQL
db = mysql.connector.connect(**DB_CONFIG)
curseur = db.cursor()

# Port série
ser = serial.Serial(PORT_SERIE, VITESSE_BAUD, timeout=1)
print(f"Lecture en cours sur {PORT_SERIE}...")

dernier_message = None

try:
    while True:
        ligne = ser.readline().decode('utf-8').strip()

        if ligne in ['>>> Présence détectée !', 'Aucune présence.'] and ligne != dernier_message:
            print(f">> Nouveau : {ligne}")
            curseur.execute("INSERT INTO mesures (valeur) VALUES (%s)", (ligne,))
            db.commit()
            dernier_message = ligne

        time.sleep(0.1)

except KeyboardInterrupt:
    print("Arrêté.")
    ser.close()
    db.close()

    import requests
import time

# Temps entre deux resets (ex: 30 minutes)
reset_interval = 60 * 30
last_reset = time.time()

try:
    while True:
        ligne = ser.readline().decode('utf-8').strip()

        # ... ton traitement habituel (insertion MySQL, etc.)

        # Reset automatique de la table toutes les 30 minutes
        if time.time() - last_reset > reset_interval:
            try:
                response = requests.get("http://localhost/web/reset_table.php")
                print("Reset de la table :", response.text)
                last_reset = time.time()
            except Exception as e:
                print("Erreur reset :", e)

        time.sleep(0.1)
except KeyboardInterrupt:
    print("Arrêté manuellement.")
    ser.close()
    db.close()
