import mysql.connector
import matplotlib.pyplot as plt
import pandas as pd
from db_config import db_host, db_user, db_pass, db_name

db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="TON_MOT_DE_PASSE",
    database="sae24"
)

query = "SELECT timestamp, valeur FROM mesures"
df = pd.read_sql(query, db)

df['timestamp'] = pd.to_datetime(df['timestamp'])
df_grouped = df.groupby('timestamp')['valeur'].apply(lambda x: (x == "presence").sum())

df_grouped.plot(kind='line', title='Détections de présence', ylabel='Présence détectée')
plt.xlabel('Horodatage')
plt.grid(True)
plt.tight_layout()
plt.show()
