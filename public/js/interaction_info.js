document.addEventListener("DOMContentLoaded", function () {
  let graphique = null;

  async function afficherDetailsPays(codePays) {
    if (!codePays) return;

    try {
      const response = await fetch(`./controllers/regions.php?action=getDetailsPays&code_pays=${codePays}`);
      if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
      const details = await response.json();

      if (!details || !details.nom_pays || !details.nom_region) return;

      const paragraphe = document.querySelector("#infoPays p");
      if (paragraphe) {
        paragraphe.innerHTML = `
          Le pays sélectionné est <span style="color: #4e8ef7;">${details.nom_pays}</span>, 
          sa région est : <span style="color: #4e8ef7;">${details.nom_region}</span>.<br><br>
          Ci-dessous se trouvent l’ensemble de ses informations ainsi que ses indicateurs.
        `;
      }
    } catch (error) {
      console.error("Erreur lors de la récupération des détails du pays :", error);
    }
  }

  function resetTable() {
    const dataRow = document.querySelector("#infoPays table tbody tr");
    if (!dataRow) return;
    for (let cell of dataRow.cells) {
      if (cell.querySelector("span")) {
        cell.querySelector("span").textContent = "";
      } else {
        cell.textContent = "";
      }
    }
  }

  function mettreAJourIndicateursTable(codePays, annee) {
    if (!codePays || !annee) return;

    const url = `./controllers/indicateurs.php?action=getIndicateursParAnneePays&annee=${annee}&code_pays=${codePays}`;
    console.log(`Récupération des données pour le pays ${codePays} en ${annee}`);

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
            return response.text(); // Change to text to inspect the response
        })
        .then(responseText => {
            try {
                const data = JSON.parse(responseText); // Parse JSON manually
                console.log("Données reçues :", data);

                const dataRow = document.querySelector("#infoPays table tbody tr");
                if (!dataRow) return console.warn("Ligne de données non trouvée");

                const formatNumber = (value, decimals = 2, cell) => {
                  if (isNaN(value) || value === "NA") {
                    cell.classList.add("cell-indisponible");
                    return "Indisponible";
                  }
                  cell.classList.remove("cell-indisponible");
                  return parseFloat(value).toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
                };

                const countryData = Array.isArray(data) ? data[0] : data;
                if (!countryData) return console.warn("Données pays non trouvées", data);

                if (dataRow.cells[0]) dataRow.cells[0].innerHTML = `${formatNumber(countryData.pib, 0, dataRow.cells[0])}${countryData.pib !== "NA" ? "&nbsp;$" : ""}`;
                if (dataRow.cells[1]) dataRow.cells[1].innerHTML = `${formatNumber(countryData.pib_par_habitant, 2, dataRow.cells[1])}${countryData.pib_par_habitant !== "NA" ? "&nbsp;$" : ""}`;
                
                if (dataRow.cells[2]) {
                  const mort = parseFloat(countryData.taux_mortalite);
                  const nat = parseFloat(countryData.taux_natalite);
                  if (isNaN(mort) || isNaN(nat) || mort === "NA" || nat === "NA") {
                    dataRow.cells[2].classList.add("cell-indisponible");
                    dataRow.cells[2].innerHTML = "Indisponible";
                  } else {
                    const ratio = (mort / nat).toFixed(2);
                    dataRow.cells[2].classList.remove("cell-indisponible");
                    dataRow.cells[2].innerHTML = `${ratio}`;
                  }
                }

                if (dataRow.cells[3]) dataRow.cells[3].innerHTML = `${formatNumber(countryData.utilisation_internet, 2, dataRow.cells[3])}${countryData.utilisation_internet !== "NA" ? "&nbsp;%" : ""}`;
                if (dataRow.cells[4]) dataRow.cells[4].innerHTML = `${formatNumber(countryData.taux_chomage, 2, dataRow.cells[4])}${countryData.taux_chomage !== "NA" ? "&nbsp;%" : ""}`;
                if (dataRow.cells[5]) dataRow.cells[5].innerHTML = `${formatNumber(countryData.esperance_vie, 2, dataRow.cells[5])}${countryData.esperance_vie !== "NA" ? "&nbsp;ans" : ""}`;
                if (dataRow.cells[6]) dataRow.cells[6].innerHTML = `${formatNumber(countryData.mortalite_infantile, 2, dataRow.cells[6])}${countryData.mortalite_infantile !== "NA" ? "&nbsp;‰" : ""}`;
                if (dataRow.cells[7]) dataRow.cells[7].innerHTML = `${formatNumber(countryData.densite_population, 2, dataRow.cells[7])}${countryData.densite_population !== "NA" ? "&nbsp;hab/km²" : ""}`;
                if (dataRow.cells[8]) dataRow.cells[8].innerHTML = `${formatNumber(countryData.consommation_electricite, 2, dataRow.cells[8])}${countryData.consommation_electricite !== "NA" ? "&nbsp;kWh" : ""}`;

                console.log("Tableau mis à jour !");
            } catch (error) {
                console.error("Erreur lors de l'analyse JSON :", error);
                console.error("Réponse brute du serveur :", responseText); // Log raw server response
                const erreurDiv = document.getElementById("erreur");
                if (erreurDiv) erreurDiv.textContent = "Impossible de récupérer les informations du pays.";
                resetTable();
            }
        })
        .catch(error => {
            console.error("Erreur lors de la récupération :", error);
            const erreurDiv = document.getElementById("erreur");
            if (erreurDiv) erreurDiv.textContent = "Impossible de récupérer les informations du pays.";
            resetTable();
        });
  }

  async function mettreAJourInfosPays(codePays, annee) {
    await afficherDetailsPays(codePays);
    mettreAJourIndicateursTable(codePays, annee);
  }

  function creerGraphiqueIndicateur(idCanvas, titre, labels, data) {
    const ctx = document.getElementById(idCanvas).getContext('2d');
    if (graphique) graphique.destroy();

    graphique = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: titre,
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 1,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { 
                    title: { display: true, text: "Année" },
                    ticks: { reverse: true } // Inverse l'axe x
                },
                y: { 
                    beginAtZero: false, 
                    title: { display: true, text: "Évolution de l'indicateur" } 
                }
            }
        }
    });
}


  function chargerDonneesEtAfficherGraphiques() {
    const pays = document.getElementById("pays1").value;
    const annee = parseInt(document.getElementById("annee").value, 10);
    const indicateur = document.getElementById("test").value;
    const erreurDiv = document.getElementById("erreur");
    erreurDiv.textContent = "";

    if (!pays || !annee || !indicateur) {
        erreurDiv.textContent = "Veuillez sélectionner un pays, une année et un indicateur.";
        resetTable();
        return;
    }

    mettreAJourInfosPays(pays, annee);

    fetch(`./controllers/indicateurs.php?action=getValeursIndicateur&code_pays=${pays}&indicateur=${indicateur}`)
        .then(response => {
            if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
            return response.text(); // Change to text to inspect the response
        })
        .then(responseText => {
            try {
                const data = JSON.parse(responseText); // Parse JSON manually
                if (!data || data.length === 0) {
                    erreurDiv.textContent = "Aucune donnée disponible pour ce pays et cet indicateur.";
                    return;
                }

                const annees = [];
                const valeurs = [];
                data.forEach(item => {
                    const year = parseInt(item.annee, 10);
                    if (year >= annee && year <= 2018) {
                        annees.push(year);
                        valeurs.push(parseFloat(item.valeur));
                    }
                });

                // Trier les années et les valeurs associées dans l'ordre croissant
                const sortedData = annees.map((year, index) => ({ year, value: valeurs[index] }))
                                         .sort((a, b) => a.year - b.year);
                const sortedAnnees = sortedData.map(item => item.year);
                const sortedValeurs = sortedData.map(item => item.value);

                if (sortedAnnees.length === 0) {
                    erreurDiv.textContent = `Aucune donnée disponible entre ${annee} et 2018.`;
                    return;
                }

                creerGraphiqueIndicateur('graphPIB', `Évolution - ${indicateur} - entre ${annee} et 2018`, sortedAnnees, sortedValeurs);
            } catch (error) {
                console.error("Erreur lors de l'analyse JSON :", error);
                console.error("Réponse brute du serveur :", responseText); // Log raw server response
                erreurDiv.textContent = "Impossible de récupérer les données du graphique.";
            }
        })
        .catch(err => {
            console.error("Erreur lors de la récupération des données graphiques :", err);
            erreurDiv.textContent = "Impossible de récupérer les données du graphique.";
        });
  }

  function chargerSelect(idSelect, url, labelKey, valueKey, defaultValue = null) {
    fetch(url)
      .then(response => {
        if (!response.ok) throw new Error(`Erreur HTTP : ${response.status}`);
        return response.json();
      })
      .then(data => {
        const select = document.getElementById(idSelect);
        if (!select) return;
        select.innerHTML = "";
        data.forEach(item => {
          const option = new Option(item[labelKey], item[valueKey]);
          select.add(option);
        });
        if (defaultValue) select.value = defaultValue;
        select.dispatchEvent(new Event("change"));
      })
      .catch(error => {
        console.error(`Erreur lors du chargement du select ${idSelect} :`, error);
      });
  }

  function chargerListesPays() {
    const url = './models/pays.php?action=getPays';
    chargerSelect('pays1', url, 'nom_pays', 'code_pays', 'AGO');
  }

  // Événements
  document.getElementById("pays1").addEventListener("change", chargerDonneesEtAfficherGraphiques);
  document.getElementById("annee").addEventListener("input", chargerDonneesEtAfficherGraphiques);
  document.getElementById("test").addEventListener("change", chargerDonneesEtAfficherGraphiques);

  // Initialisation
  chargerListesPays();
  setTimeout(chargerDonneesEtAfficherGraphiques, 1000);
});