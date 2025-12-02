<?php // Vue pour les parties A et B ?>
<table id="graphTable">
  <tr>
    <td rowspan="5" style="padding: 20px;">
      <label for="pays1">Sélectionnez un Pays :</label><br>
      <select id="pays1" required>
        <option>Veuillez sélectionner un pays</option>
      </select>
      
      <label for="annee">Veuillez choisir une année : </label><br>
      <input id="annee" type="number" value="2000" min="1960" max="2018" style="width: 92%; padding: 8px; margin-bottom: 15px;"><br><br>

      <label for="test">La liste des indicateurs :</label><br>
      <select id="test">
        <option value="pib" selected>PIB</option>
        <option value="esperance_vie">Espérance de vie</option>
        <option value="densite_population">Population</option>
        <option value="taux_natalite">Taux de natalité</option>
        <option value="taux_mortalite">Taux de mortalité</option>
        <option value="consommation_electricite">Consommation d'Électricité</option>
        <option value="pib_par_habitant">PIB par habitant</option>
        <option value="mortalite_infantile">Mortalité infantile</option>
        <option value="taux_chomage">Taux de chômage</option> 
      </select>

      <br><br>
      <div id="erreur" style="color:red;"></div>
    </td>
  </tr> 

  <tr>
    <td rowspan="1" style="padding: 50px; background-color: #ffffff; border-radius:5px;">
      <div id="infoPays">
        <p style="font-size: 18px; font-weight: bold; color: #333; margin-bottom: 20px;">
          Le pays sélectionné est <span style="color: #4e8ef7;">BourseTrack</span>, sa région est : <span style="color: #4e8ef7;">NomDeLaRégion</span>.<br><br>
          Ci-dessous se trouvent l’ensemble de ses informations ainsi que ses indicateurs.
        </p>
        
        <!-- Tableau des indicateurs -->
        <table style="width: 100%; margin-top: 30px; border-collapse: collapse; border: 1px solid #3366CC; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
          <thead style="background-color: #4e8ef7; color: white; font-weight: bold;">
            <tr>
              <th>PIB</th>
              <th>PIB par habitant</th>
              <th>Taux de mortalité / natalité</th>
              <th>Utilisation d'internet</th>
              <th>Taux de chômage</th>
              <th>Espérance de vie</th>
              <th>Mortalité infantile</th>
              <th>Densité de population</th>
              <th>Consommation d’électricité</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td id="pib" style="text-align: center;">-</td>
              <td id="pib_par_habitant" style="text-align: center;">-</td>
              <td id="mortalite_natalite" style="text-align: center;">-</td>
              <td id="utilisation_internet" style="text-align: center;">-</td>
              <td id="taux_chomage" style="text-align: center;">-</td>
              <td id="esperance_vie" style="text-align: center;">-</td>
              <td id="mortalite_infantile" style="text-align: center;">-</td>
              <td id="densite_population" style="text-align: center;">-</td>
              <td id="consommation_electricite" style="text-align: center;">-</td>
            </tr>
          </tbody>
        </table>
      </div>
    </td>
  </tr>

  <tr>
    <td colspan="1" style="background-color: #ffffff; border-radius:5px;">
      <canvas id="graphPIB"></canvas>
    </td>
  </tr>
</table>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="./public/js/interaction_info.js"></script>