function updatePresence() {
    fetch('data.php')
        .then(response => response.json())
        .then(data => {
            const now = new Date();
            const presenceTime = new Date(data.timestamp);
            const diff = (now - presenceTime) / 1000; // secondes

            const message = document.getElementById("message");
            const horodatage = document.getElementById("timestamp");
            const total = document.getElementById("total");

            // Gestion couleur + message
            if (data.etat === "présence" && diff <= 10) {
                document.body.className = "vert";
                message.textContent = "Présence détectée";
            } else {
                document.body.className = "rouge";
                message.textContent = "Aucune présence détectée";
            }

            // Format de la date lisible : Aujourd’hui, Hier, sinon brut
            if (data.timestamp) {
                const nowDate = new Date();
                const diffDays = Math.floor((nowDate - presenceTime) / (1000 * 60 * 60 * 24));
                let affichage = "";

                const heure = presenceTime.toLocaleTimeString("fr-FR");

                if (
                    presenceTime.toDateString() === nowDate.toDateString()
                ) {
                    affichage = `Aujourd'hui à ${heure}`;
                } else if (diffDays === 1) {
                    affichage = `Hier à ${heure}`;
                } else {
                    affichage = `${presenceTime.toLocaleDateString("fr-FR")} à ${heure}`;
                }

                horodatage.textContent = `Dernière détection : ${affichage}`;
            } else {
                horodatage.textContent = "Aucune détection encore enregistrée.";
            }

            total.textContent = data.total ?? 0;
        })
        .catch(error => {
            console.error("Erreur AJAX :", error);
        });
}

setInterval(updatePresence, 3000);
window.onload = updatePresence;

document.getElementById("reset-btn").addEventListener("click", () => {
    fetch("reset_table.php", { method: "POST" })
        .then(response => response.text())
        .then(result => {
            console.log("Reset effectué :", result);
            document.getElementById("reset-confirm").style.display = "block";
            updatePresence(); // Recharge à 0
        })
        .catch(error => {
            console.error("Erreur reset :", error);
        });
});