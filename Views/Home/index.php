<!-- START HERO-->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Ecoride, le covoiturage qui a du sens</h1>
    </div>
</section>
<!-- END HERO-->

<!-- START RECHERCHE-->
<section class="search-form" >
    <div class="form-container">
        <h3>Où voulez-vous aller ?</h3>
        <div class="form-carpool d-flex justify-content-center align-items-center flex-column">
            <form action="/carpools" method="GET" class="form needs-validation" novalidate>
                <div class="inputs-group">
                    <div>
                        <input type="text" name="departure" placeholder="Départ" class="form-control" id="departure" required>
                    </div>
                    <div>
                        <input type="text" name="arrival" placeholder="Arrivée" class="form-control" id="arrival" required>
                    </div>
                    <div>
                        <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d') ?>" id="date" required>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn">Rechercher</button>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- END RECHERCHE-->

<!-- START PRESENTATION-->
<section class="container">
    <div class="presentation d-flex justify-content-center align-items-center">
        <div class="presentation-image">
            <img src="./assets/images/presentation.jpeg" alt="Illustration de covoiturage">
        </div>
        <div class="presentation-content d-flex flex-column">
            <h2 class="presentation-title">Qui sommes-nous ?</h2><br>
            <p class="presentation-text">Ecoride est une startup française innovante née de la volonté de transformer nos habitudes de déplacement. Fondée par une équipe passionnée par la nature, nous croyons fermement que chaque trajet partagé est un pas vers un avenir plus durable.</p>
        </div>
    </div>
</section>
<!-- END PRESENTATION-->

<!-- START MISSION-->
<section class="container">
    <div class="mission d-flex justify-content-center align-items-center">
        <div class="mission-content d-flex flex-column">
            <h2 class="mission-title">Notre mission</h2><br>
            <p class="mission-text">Réduire l'impact environnemental de nos déplacements en démocratisant le covoiturage. Nous connectons les conducteurs et passagers soucieux de l'environnement pour créer une communauté de voyageurs soucieux de contribuer à la protection de notre planète.</p>
        </div>
        <div class="mission-image">
            <img src="./assets/images/mission.jpeg" alt="paysage nature">
        </div>
    </div>
</section>
<!-- END MISSION-->

<!-- START VALEURS-->
<section class="valeurs d-flex flex-column align-items-center">
    <h2>Pourquoi choisir Ecoride ?</h2>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="ecology d-flex flex-column justify-content-center align-items-center">
            <div class="ecology-image">
                <img src="./assets/images/pictoEcologie.png" alt="Pictogramme écologie">
            </div>
            <div class="ecology-content d-flex flex-column justify-content-center align-items-center">
                <h3 class="ecology-title">Engagement écologique</h3>
                <p>Nous accordons une priorité aux véhicules électriques pour réduire de manière significative les emissions de CO2 et contribuer ensemble à la protection de l’environnement.</p>
            </div>
        </div>
        <div class="social d-flex flex-column justify-content-center align-items-center">
            <div class="social-image">
                <img src="./assets/images/pictoSocial.png" alt="Pictogramme social">
            </div>
            <div class="social-content d-flex flex-column justify-content-center align-items-center">
                <h3 class="social-title">Economique et social</h3>
                <p>Partagez vos frais de transports et partez à la rencontre des personnes qui partagent vos valeurs.</p>
            </div>
        </div>
        <div class="security d-flex flex-column justify-content-center align-items-center">
            <div class="security-image">
                <img src="./assets/images/pictoPlateforme.png" alt="Pictogramme plateforme">
            </div>
            <div class="security-content d-flex flex-column justify-content-center align-items-center">
                <h3 class="security-title">Simplicité et sécurité</h3>
                <p>Nous proposons une plateforme intuitive et sécurisée. Trouvez le conducteur qui vous convient grâce aux avis déposés par les précédents voyageurs.</p>
            </div>
        </div>
    </div>
</section>
<!-- END VALEURS-->

<!-- START DERNIERE SECTION-->
<section class="container">
    <div class="section d-flex justify-content-center align-items-center">
        <div class="section-image">
            <img src="./assets/images/Covoitureurs.jpeg" alt="Photographie de covoitureurs">
        </div>
        <div class="section-content d-flex flex-column">
            <h2 class="section-title">Rejoignez le mouvement !</h2><br>
            <p class="section-text">Que vous soyez conducteur cherchant à rentabiliser vos trajets ou passager en quête d'une alternative écologique, notre plateforme vous accompagne dans tous vos déplacements.</p>
            <p class="section-text"><strong>Ensemble, construisons un avenir plus vert, un trajet à la fois.</strong></p>
        </div>
    </div>
</section>
<!-- END DERNIERE SECTION-->

