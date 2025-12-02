<?php
// Vue pour les parties C et D
?>
<table id = "graphTable">
  <tr>
    <td rowspan="5">
        <label for="pays1">Pays 1 :</label><br>
        <select id="pays1"><option value="" disabled selected>Choisissez un pays</option></select><br><br>

        <label for="pays2">Pays 2 :</label><br>
        <select id="pays2"><option value="" disabled selected>Choisissez un pays</option></select><br><br>

        <label for="indicateur">Indicateur :</label><br>
        <select id="indicateur"><option value="" disabled selected>Choisissez un indicateur</option></select><br><br>

        <br><br>
        <button id="bouton_comparer">Comparer</button>
        <div id="erreur"></div>
    </td>
    <td>
        <div class="kpi-box" id="kpi-pays1">
            <h3>Pays 1</h3>
            <p>IDH : <span id="idh-pays1">-</span></p>
        </div>
    </td>
    <td>
        <div class="kpi-box" id="kpi-pays2">
            <h3>Pays 2</h3>
            <p>IDH : <span id="idh-pays2">-</span></p>
        </div>
    </td>
  </tr>
  <tr>
    <td rowspan="4" colspan="2" class="graph-cell">
        <div class="graph-container">
            <canvas id="comparaisonChart" ></canvas>
        </div>
    </td>
  </tr>

</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./public/js/graphiques.js"></script>
<script src="./public/js/interactions.js"></script>

