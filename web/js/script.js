let chart = null;

// Fonction principale de mise à jour de la présence
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

// Gestion couleur + message en temps réel
if (data.etat === "présence" && diff <= 10) {
document.body.className = "vert";
message.textContent = "Présence détectée";
} else {
document.body.className = "rouge";
message.textContent = "Aucune présence détectée";
}

// Format horodatage
if (data.timestamp) {
const nowDate = new Date();
const diffDays = Math.floor((nowDate - presenceTime) / (1000 * 60 * 60 * 24));
const heure = presenceTime.toLocaleTimeString("fr-FR");
let affichage = "";

if (presenceTime.toDateString() === nowDate.toDateString()) {
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
console.error("Erreur AJAX (presence) :", error);
});
}

// Fonction de mise à jour du graphique
function updateGraph() {
fetch('history.php')
.then(response => response.json())
.then(data => {
const labels = data.map(entry => new Date(entry.timestamp).toLocaleTimeString("fr-FR"));
const valeurs = data.map(entry => entry.valeur === '>>> Présence détectée !' ? 1 : 0);

if (!chart) {
const ctx = document.getElementById('chart').getContext('2d');
chart = new Chart(ctx, {
type: 'line',
data: {
labels: labels,
datasets: [{
label: 'Présence (1) / Absence (0)',
data: valeurs,
borderWidth: 2,
borderColor: 'blue',
fill: false,
tension: 0.3
}]
},
options: {
responsive: true,
animation: false,
scales: {
y: {
min: 0,
max: 1,
ticks: {
stepSize: 1,
callback: value => value === 1 ? 'Présence' : 'Absence'
}
}
}
}
});
} else {
chart.data.labels = labels;
chart.data.datasets[0].data = valeurs;
chart.update();
}
})
.catch(error => {
console.error("Erreur AJAX (graphique) :", error);
});
}

// Gestion du bouton reset
document.getElementById("reset-btn").addEventListener("click", () => {
fetch("reset_table.php", { method: "POST" })
.then(response => response.text())
.then(result => {
console.log("Reset effectué :", result);
document.getElementById("reset-confirm").style.display = "block";
updatePresence(); // rafraîchit les valeurs
updateGraph(); // rafraîchit le graphique
})
.catch(error => {
console.error("Erreur reset :", error);
});
});

// Rafraîchissement en live toutes les 3 secondes
setInterval(() => {
updatePresence();
updateGraph();
}, 3000);

window.onload = () => {
updatePresence();
updateGraph();
};