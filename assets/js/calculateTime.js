document.addEventListener('DOMContentLoaded', () => {
    const beginInput = document.getElementById('create_carpool_form_begin');
    const endInput = document.getElementById('create_carpool_form_end');

    if (!beginInput || !endInput) {
        console.warn("Champs d’heure introuvables");
        return;
    }

    // Fonction de calcul de l'heure de fin
    function computeArrivalTime(route) {
        const durationSec = route.summary.totalTime;
        const beginStr = beginInput.value;
        if (!beginStr) return;

        const [hours, minutes] = beginStr.split(':').map(Number);
        const beginDate = new Date();
        beginDate.setHours(hours, minutes, 0, 0);

        const endDate = new Date(beginDate.getTime() + durationSec * 1000);
        const endHours = String(endDate.getHours()).padStart(2, '0');
        const endMinutes = String(endDate.getMinutes()).padStart(2, '0');
        const endStr = `${endHours}:${endMinutes}`;
        console.log("On écrit dans l'input fin :", endStr);
        endInput.value = endStr;

        console.log("⏱ Heure de fin calculée :", endStr);
    }

    // Attente de l’existence de window.routingControl
    function waitForRoutingControl(retry = 0) {
        if (window.routingControl && typeof window.routingControl.on === 'function') {
            console.log("📦 routingControl prêt, on écoute routesfound");

            window.routingControl.on('routesfound', function(e) {
                if (e.routes.length > 0) {
                    computeArrivalTime(e.routes[0]);
                }
            });

            // déclenchement manuel au cas où un itinéraire est déjà dispo
            const existingRoutes = window.routingControl.getRoutes?.();
            if (existingRoutes && existingRoutes.length > 0) {
                computeArrivalTime(existingRoutes[0]);
            }

        } else if (retry < 10) {
            setTimeout(() => waitForRoutingControl(retry + 1), 300);
        } else {
            console.warn("❌ routingControl toujours indisponible après plusieurs essais.");
        }
    }

    waitForRoutingControl();

    // Si l'utilisateur change l’heure de départ manuellement
    beginInput.addEventListener('input', () => {
        const routes = window.routingControl?.getRoutes?.();
        if (routes && routes.length > 0) {
            computeArrivalTime(routes[0]);
        }
    });
});
