// Stocker les instances des graphiques pour éviter les doublons
let chartInstances = {};

function creerGraphiqueComparaison(idCanvas, titre, labels, data1, data2, label1, label2) {

    const canvas = document.getElementById(idCanvas);
    if (!canvas) {
        console.warn(`Canvas avec l'id '${idCanvas}' introuvable — graphique ignoré.`);
        return;
    }

    const ctx = canvas.getContext("2d");
    
    
    // Supprime l'ancien graphe s'il existe déjà
    if (chartInstances[idCanvas]) {
        chartInstances[idCanvas].destroy();
    }

    chartInstances[idCanvas] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: label1,
                    data: data1,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0, 0, 255, 0.1)',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: label2,
                    data: data2,
                    borderColor: 'orange',
                    backgroundColor: 'rgba(255, 165, 0, 0.1)',
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: titre,
                    color: 'black',
                    font: { size: 18 }
                },
                legend: {
                    labels: { color: 'black' }
                }
            },
            scales: {
                x: {
                    ticks: { color: 'black' },
                    title: {
                        display: true,
                        text: "Années",
                        color: 'black',
                        font: { size: 14 }
                    },
                    reverse: true
                },
                y: {
                    ticks: { color: 'black' },
                    title: {
                        display: true,
                        text: "Valeurs",
                        color: 'black',
                        font: { size: 14 }
                    },
                    beginAtZero: false
                }
            }
        }
    });
}



document.addEventListener("DOMContentLoaded", function () {


    // Fonction générique pour créer un graphique
    function creerGraphique(idCanvas, titre, labels, data, labelDataset, couleur) {

        const canvas = document.getElementById(idCanvas);
        if (!canvas) {
            console.warn(`Canvas avec l'id '${idCanvas}' introuvable — graphique ignoré.`);
            return;
        }

        const ctx = canvas.getContext("2d");
        

        // Détruire le graphique existant s'il existe
        if (chartInstances[idCanvas]) {
            chartInstances[idCanvas].destroy();
        }

        const donnees = {
            labels: labels,
            datasets: [{
                label: labelDataset,
                data: data,
                borderColor: couleur,
                backgroundColor: `rgba(0, 0, 255, 0.1)`,
                borderWidth: 2,
                pointRadius: 3,
                tension: 0.3,
                fill: true
            }]
        };

        const config = {
            type: 'line',
            data: donnees,
            options: {
                responsive: true, // Activer le redimensionnement automatique
                maintainAspectRatio: false, // Désactiver le maintien des proportions
                plugins: {
                    legend: {
                        labels: {
                            color: 'black',
                            font: { size: 14 }
                        }
                    },
                    title: {
                        display: true,
                        text: titre,
                        color: 'black',
                        font: { size: 18 }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: 'black' },
                        title: {
                            display: true,
                            text: "Années",
                            color: 'black',
                            font: { size: 14 }
                        }
                    },
                    y: {
                        ticks: { color: 'black' },
                        title: {
                            display: true,
                            text: "Valeurs",
                            color: 'black',
                            font: { size: 14 }
                        },
                        beginAtZero: false
                    }
                }
            }
        };

        // Créer une nouvelle instance de graphique et la stocker
        chartInstances[idCanvas] = new Chart(ctx, config);
    }


    // Fonction pour charger les données d'un graphique
    function chargerDonnees(action, idCanvas, titre, labelDataset, couleur) {
        fetch(`/DED/controllers/indicateurs.php?action=${action}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP ! statut : ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(`Données reçues pour ${idCanvas}:`, data);
    
                const canvas = document.getElementById(idCanvas);
                if (!canvas) {
                    console.warn(`Canvas avec l'id '${idCanvas}' introuvable — graphique ignoré.`);
                    return;
                }
    
                if (data.error) {
                    console.error(`Erreur pour le graphique ${idCanvas}:`, data.error);
                    canvas.parentElement.innerHTML = `<p style="color: red;">Erreur : ${data.error}</p>`;
                    return;
                }
    
                const labels = Object.keys(data).sort();

                const valeurs = labels.map(annee => data[annee]);
    
                console.log(`Labels pour ${idCanvas}:`, labels);
                console.log(`Valeurs pour ${idCanvas}:`, valeurs);
    
                creerGraphique(idCanvas, titre, labels, valeurs, labelDataset, couleur);
            })
            .catch(error => {
                console.error(`Erreur lors du chargement des données pour ${idCanvas}:`, error);
                const canvas = document.getElementById(idCanvas);
                if (canvas) {
                    canvas.parentElement.innerHTML = `<p style="color: red;">Erreur lors du chargement des données.</p>`;
                }
            });
    }

    function chargerDonneesMultiplesCourbes(action, idCanvas, titre) {
        fetch(`/DED/controllers/indicateurs.php?action=${action}`)
            .then(response => response.json())
            .then(data => {
                const canvas = document.getElementById(idCanvas);
                if (!canvas) {
                    console.warn(`Canvas avec l'id '${idCanvas}' introuvable — graphique ignoré.`);
                    return;
                }
    
                if (data.error) {
                    console.error(`Erreur pour le graphique ${idCanvas}:`, data.error);
                    canvas.parentElement.innerHTML = `<p style="color: red;">Erreur : ${data.error}</p>`;
                    return;
                }
    
                const allYears = new Set();
                Object.values(data).forEach(regionData => {
                    Object.keys(regionData).forEach(year => allYears.add(year));
                });
                const labels = Array.from(allYears).sort();
                
    
                const couleurs = ['red', 'blue', 'green', 'orange', 'purple', 'brown', 'teal', 'pink'];
                let colorIndex = 0;
    
                const datasets = Object.entries(data).map(([region, values]) => {
                    const dataParAnnee = labels.map(annee => values[annee] ?? null);
                    return {
                        label: region,
                        data: dataParAnnee,
                        borderColor: couleurs[colorIndex++ % couleurs.length],
                        fill: false,
                        tension: 0.3,
                        spanGaps: true
                    };
                });
    
                const ctx = canvas.getContext("2d");
    
                chartInstances[idCanvas] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: titre,
                                color: 'black',
                                font: { size: 18 }
                            },
                            legend: {
                                labels: { color: 'black' }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: 'black' },
                                title: {
                                    display: true,
                                    text: "Années",
                                    color: 'black'
                                }
                            },
                            y: {
                                ticks: { color: 'black' },
                                title: {
                                    display: true,
                                    text: "Ratio natalité / mortalité",
                                    color: 'black'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error(`Erreur lors du chargement des données pour ${idCanvas}:`, error);
                const canvas = document.getElementById(idCanvas);
                if (canvas) {
                    canvas.parentElement.innerHTML = `<p style="color: red;">Erreur lors du chargement des données.</p>`;
                }
            });
    }

    // Cette fonction est utilisée pour charger les données du graphique de PIB par région
// Faudra vérifier quel id de canvas on utilise

function chargerGraphiquePIBParRegion(idCanvas, action, titre) {
    fetch(`/DED/controllers/indicateurs.php?action=${action}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP ! statut : ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const canvas = document.getElementById(idCanvas);
            if (!canvas) {
                console.warn(`Canvas avec l'id '${idCanvas}' introuvable — graphique ignoré.`);
                return;
            }

            if (data.error) {
                console.error(`Erreur pour le graphique ${idCanvas}:`, data.error);
                canvas.parentElement.innerHTML = `<p style="color: red;">Erreur : ${data.error}</p>`;
                return;
            }

            // Calculate total GDP per region and sort regions by total GDP
            const regions = Object.keys(data).sort((a, b) => {
                const totalA = data[a].reduce((sum, pays) => sum + pays.moyenne_pib, 0);
                const totalB = data[b].reduce((sum, pays) => sum + pays.moyenne_pib, 0);
                return totalB - totalA; // Sort descending
            });

            const couleurs = ['#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', '#edc948', '#b07aa1', '#ff9da7'];
            let colorIndex = 0;

            const datasets = [];

            regions.forEach(region => {
                const paysData = data[region];
                paysData.forEach(pays => {
                    datasets.push({
                        label: pays.pays,
                        data: regions.map(r => (r === region ? pays.moyenne_pib : 0)),
                        backgroundColor: couleurs[colorIndex++ % couleurs.length],
                        stack: region
                    });
                });
            });

            const ctx = canvas.getContext("2d");

            // Supprimer l'ancien graphique si existant
            if (chartInstances[idCanvas]) {
                chartInstances[idCanvas].destroy();
            }

            chartInstances[idCanvas] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: regions,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: titre,
                            color: 'black',
                            font: { size: 18 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const pays = context.dataset.label;
                                    const valeur = context.raw;
                                    return `${pays} : ${valeur.toLocaleString()} $`;
                                }
                            }
                        },
                        legend: {
                            display: false // Désactiver la légende pour éviter trop d'éléments
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                color: 'black',
                                align: 'center'
                            },
                            title: {
                                display: true,
                                text: "Régions",
                                color: 'black'
                            }
                        },
                        y: {
                            stacked: true,
                            ticks: { color: 'black' },
                            title: {
                                display: true,
                                text: "PIB ($)",
                                color: 'black'
                            },
                            beginAtZero: true
                        }
                    },
                    elements: {
                        bar: {
                            barThickness: 50, 
                            maxBarThickness: 50 
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error(`Erreur lors du chargement des données pour ${idCanvas}:`, error);
            const canvas = document.getElementById(idCanvas);
            if (canvas) {
                canvas.parentElement.innerHTML = `<p style="color: red;">Erreur lors du chargement des données.</p>`;
            }
        });
}



    // Charger les données pour les graphiques
    if (document.getElementById('pibChart')) {
        chargerGraphiquePIBParRegion(
            'pibChart',
            'getMoyennePIBMondial',
            "Répartition du PIB Moyen par Région et Pays (Top 5)",
        );
    }

    if (document.getElementById('esperanceVieChart')) {
        chargerDonnees(
            'getEsperanceVieMondiale',
            'esperanceVieChart',
            "Évolution de l'Espérance de Vie Mondiale",
            "Moyenne de l'Espérance de Vie (en années)",
            "green"
        );
    } 

    if (document.getElementById('ratioRegionsCurve')) {
        chargerDonneesMultiplesCourbes(
            'getRatioParRegionParAnnee',
            'ratioRegionsCurve',
            "Évolution du Ratio Natalité / Mortalité par Région"
        );
    }
    
});


