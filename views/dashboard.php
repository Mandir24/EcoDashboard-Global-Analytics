<!-- filepath: c:\xampp\htdocs\DED\views\dashboard.php -->
<table id="graphTable">
    <tr>
        <td class="graph-cell">
            <div class="graph-container">
                <canvas id="pibChart"></canvas>
            </div>
        </td>
        <td class="graph-cell">
            <div class="graph-container">
                <canvas id="esperanceVieChart"></canvas>
            </div>
        </td>
    </tr>
    <tr>
    <td class="graph-cell">
            <div class="graph-container">
                <div id="map-container">
                    <div id="controls">
                        <select id="indicatorSelect">
                            <option value="pib">PIB</option>
                            <option value="esperance_vie">Espérance de vie</option>
                            <option value="densite_population">Population</option>
                            <option value="taux_natalite">Taux de natalité</option>
                            <option value="taux_mortalite">Taux de mortalité</option>
                            <option value="consommation_electricite">Consommation d'Éléctricité</option>
                            <option value="pib_par_habitant">PIB par habitant</option>
                            <option value="mortalite_infantile">Mortalité infantile</option>
                            <option value="taux_chomage">Taux de chômage</option> 
                        </select>
                        <label for="yearSelect">Année :</label>
                        <input type="number" id="yearSelect" value="2008" min="1960" max="2018">
                    </div>
                    <div id="map"></div>
                </div>
            </div>
        </td>
        <td class="graph-cell">
            <div class="graph-container">
                <canvas id="ratioRegionsCurve"></canvas>
            </div>
        </td>
    </tr>
</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./public/js/graphiques.js"></script>
<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="./public/js/map.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const indicatorSelect = document.getElementById("indicatorSelect");
        const yearSelect = document.getElementById("yearSelect");

        // Gestion du double-clic pour afficher les pays d'une région
        document.addEventListener("regionDblClick", function (event) {
            const region = event.detail.region;
            const indicator = indicatorSelect.value;
            const year = yearSelect.value;

            // Appeler une fonction PHP pour récupérer les données des pays
            fetch(`./controllers/indicateurs.php?action=getDistributionIndicateurParPays&idIndicateur=${indicator}&annee=${year}&region=${region}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (typeof updateCountries === "function") {
                        updateCountries(indicator, year, region, data);
                    } else {
                        console.error("La fonction updateCountries n'est pas définie.");
                    }
                })
                .catch(error => console.error("Erreur lors du chargement des données des pays :", error));
        });
    });
</script>


    