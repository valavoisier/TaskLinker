// Ce fichier est chargé APRÈS jQuery et Select2 dans base.html.twig
// Il initialise Select2 sur tous les <select multiple>

// Attendre que le DOM soit prêt
(function() {
    'use strict';
    
    // Vérifier que jQuery et Select2 sont disponibles
    if (typeof window.$ === 'undefined' || typeof window.jQuery === 'undefined') {
        console.warn('jQuery not loaded, Select2 initialization skipped');
        return;
    }
    
    if (typeof $.fn.select2 === 'undefined') {
        console.warn('Select2 not loaded, initialization skipped');
        return;
    }
    
    // Initialiser Select2 sur tous les selects multiples
    $(document).ready(function() {
        $('select[multiple]').select2({
            placeholder: 'Sélectionnez...',
            allowClear: true,
            width: '100%'
        });
        console.log('Select2 initialized on', $('select[multiple]').length, 'elements');
        
        // Initialiser Select2 sur les selects simples aussi
        $('select:not([multiple])').select2({
            placeholder: 'Sélectionnez...',
            width: '100%'
        });
        console.log('Select2 (simple) initialized on', $('select:not([multiple])').length, 'elements');
    });
})();
