
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('telephone').addEventListener('input', function () {
        let telephone = this.value;
        

        if (telephone.length > 0) {
            fetch(`/commande/rechercher-client?telephone=${telephone}`)
                .then(response => response.json())
                .then(data => {
                    if (data.client) {

                        document.getElementById('client-info').textContent = `Client trouvé: ${data.client.nom} ${data.client.prenom}`;
                    } else {

                        document.getElementById('client-info').textContent = 'Client non trouvé';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche du client:', error);
                });
        } else {

            document.getElementById('client-info').textContent = '';
        }
    });


    document.getElementById('ajouter-article').addEventListener('click', function () {
        let articleId = document.getElementById('article').value;
        let prix = document.getElementById('prix').value;
        let quantite = document.getElementById('quantite').value;

        if (articleId && prix && quantite) {

            let table = document.getElementById('articles-table');
            let row = table.insertRow();
            row.insertCell(0).textContent = articleId;
            row.insertCell(1).textContent = prix;
            row.insertCell(2).textContent = quantite;
            row.insertCell(3).innerHTML = '<button class="supprimer-article">Supprimer</button>';


            row.querySelector('.supprimer-article').addEventListener('click', function () {
                table.deleteRow(row.rowIndex);
            });
        } else {
            alert('Veuillez remplir tous les champs de l\'article.');
        }
    });


    document.getElementById('valider-commande').addEventListener('click', function () {
        let clientId = document.getElementById('client-id').value;
        let details = [];


        let rows = document.getElementById('articles-table').rows;
        for (let i = 0; i < rows.length; i++) {
            let cells = rows[i].cells;
            let articleId = cells[0].textContent;
            let prix = cells[1].textContent;
            let quantite = cells[2].textContent;
            details.push({ articleId, prix, quantite });
        }

        if (clientId && details.length > 0) {

            fetch('/commande/valider', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    clientId: clientId,
                    details: details,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Commande validée avec succès!');

                } else {
                    alert('Erreur lors de la validation de la commande');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la validation de la commande:', error);
            });
        } else {
            alert('Veuillez sélectionner un client et ajouter des articles.');
        }
    });
});
