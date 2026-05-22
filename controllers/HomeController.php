<?php

class HomeController
{
    // ---------------------------------------------------------------
    // Page d'accueil : catégories + restaurants mis en avant
    // ---------------------------------------------------------------
    public function index()
    {
        $allCategories  = [];
        $featuredRestos = [];
        $error_message  = '';

        try {
            $catManager   = new Category();
            $restoManager = new Restaurant();

            $allCategories  = $catManager->listAll();
            $featuredRestos = $restoManager->listRestaurants(true, true);
        } catch (Exception $e) {
            // On laisse la page se charger ; la vue affichera l'alerte.
            $error_message = $e->getMessage();
        }

        return compact('allCategories', 'featuredRestos', 'error_message');
    }
}
