document.addEventListener("DOMContentLoaded", function () {
    // Mapping des noms des indicateurs
    const mappingIndicateurs = {
        'pib': 'PIB',
        'esperance_vie': 'Espérance de Vie',
        'densite_population': 'Population',
        'taux_natalite': 'Taux de Natalité',
        'taux_mortalite': 'Taux de Mortalité',
        'utilisation_internet': 'Utilisation d\'Internet',
        'consommation_electricite': 'Consommation d\'Électricité',
        'pib_par_habitant': 'PIB par Habitant',
        'mortalite_infantile': 'Mortalité Infantile',
        'taux_chomage': 'Taux de Chômage'
    };

    // Reverse mapping pour retrouver la clé à partir de la valeur formatée
    const reverseMappingIndicateurs = Object.fromEntries(
        Object.entries(mappingIndicateurs).map(([key, value]) => [value, key])
    );

    // Fonction pour formater les noms des indicateurs
    function formaterNomIndicateur(nom) {
        return mappingIndicateurs[nom] || nom; // Utilise le mapping ou retourne le nom original si non trouvé
    }

    // Fonction pour retrouver la clé d'un indicateur à partir de son nom formaté
    function retrouverCleIndicateur(nomFormate) {
        return reverseMappingIndicateurs[nomFormate] || nomFormate; // Utilise le reverse mapping ou retourne le nom formaté si non trouvé
    }

    // Fonction générique pour remplir un select à partir d'une API
    function chargerSelect(idSelect, url, labelKey, valueKey) {
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP : ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const select = document.getElementById(idSelect);
                if (!select) {
                    console.warn(`Élément #${idSelect} introuvable.`);
                    return;
                }

                select.innerHTML = ''; // Réinitialise les options du select
                data.forEach(item => {
                    const label = idSelect === 'indicateur' ? formaterNomIndicateur(item[labelKey]) : item[labelKey];
                    const option = new Option(label, item[valueKey]);
                    select.add(option);
                });
            })
            .catch(error => {
                console.error(`Erreur lors du chargement du select #${idSelect} :`, error);
            });
    }

    // Fonction pour remplir les 2 listes de pays (pays1 & pays2)
    function chargerListesPays() {
        const url = '/DED/models/pays.php?action=getPays';
        chargerSelect('pays1', url, 'nom_pays', 'code_pays');
        chargerSelect('pays2', url, 'nom_pays', 'code_pays');
    }

    // Fonction pour remplir la liste des indicateurs
    function chargerListeIndicateurs() {
        const url = '/DED/models/indicateur.php?action=getIndicateurs';
        chargerSelect('indicateur', url, 'nom_indicateur', 'id_indicateur');
    }

    // Gestion du bouton de comparaison
    const btn = document.getElementById("bouton_comparer");
    if (btn) {
        btn.addEventListener("click", function () {
            const pays1 = document.getElementById("pays1").value;
            const pays2 = document.getElementById("pays2").value;
            const indicateurFormate = document.getElementById("indicateur").value;
            const indicateur = retrouverCleIndicateur(indicateurFormate); // Retrouve la clé réelle de l'indicateur
            const erreurDiv = document.getElementById("erreur");

            erreurDiv.textContent = "";

            if (!pays1 || !pays2 || !indicateur) {
                erreurDiv.textContent = "Veuillez sélectionner les deux pays et un indicateur.";
                return;
            }

            if (pays1 === pays2) {
                erreurDiv.textContent = "Les deux pays doivent être différents.";
                return;
            }


            Promise.all([
                fetch(`/DED/controllers/indicateurs.php?action=getIdhParPays&code=${pays1}`).then(r => r.json()),
                fetch(`/DED/controllers/indicateurs.php?action=getIdhParPays&code=${pays2}`).then(r => r.json())
            ])
            .then(([idh1, idh2]) => {
                const idhEl1 = document.getElementById("idh-pays1");
                const idhEl2 = document.getElementById("idh-pays2");
                idhEl1.textContent = idh1?.idh ? `${Number(idh1.idh).toFixed(2)}%` : "N/A";
                idhEl2.textContent = idh2?.idh ? `${Number(idh2.idh).toFixed(2)}%` : "N/A";
            });

            fetch(`/DED/controllers/indicateurs.php?action=comparerPays&pays1=${pays1}&pays2=${pays2}&indicateur=${indicateur}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        erreurDiv.textContent = `Erreur : ${data.error}`;
                        return;
                    }

                    // Appel à la fonction du graphique avec le nom formaté
                    creerGraphiqueComparaison(
                        'comparaisonChart',
                        `Évolution de ${formaterNomIndicateur(data.indicateur)} pour ${data.nomPays1} et ${data.nomPays2}`,
                        data.annees,
                        data.valeurs1,
                        data.valeurs2,
                        data.nomPays1,
                        data.nomPays2
                    );
                })
                .catch(error => {
                    console.error("Erreur lors de la requête :", error);
                    erreurDiv.textContent = "Une erreur s'est produite lors de la comparaison.";
                });
        });
    }

    // Chargement au démarrage
    chargerListesPays();
    chargerListeIndicateurs();
});