/**
 * Bon Trouver - Per-Listing On-Demand Translator
 * Safe, content-aware client-side translation toggle with 0ms in-memory cache
 * Translates ad title, description, breadcrumb, section titles, badges, button texts, location, and attributes.
 */

const DETAIL_PHRASES = {
    fr: {
        // Section card titles
        "Overview & Key Details": "Aperçu et caractéristiques clés",
        "Description": "Description",
        "Location & Neighbourhood": "Emplacement et quartier",
        "Bontrouver Buyer Safety Tips": "Conseils de sécurité pour les acheteurs",
        "You May Also Like": "Annonces similaires qui pourraient vous plaire",
        "Similar Listings You May Like": "Annonces similaires qui pourraient vous plaire",
        "Tags & Highlights:": "Balises et points forts :",

        // Badges
        "Verified Content": "Contenu vérifié",
        "Approximate Area": "Zone approximative",
        "Verified": "Vérifié",
        "Verified Seller": "Vendeur vérifié",
        "FEATURED": "EN VEDETTE",
        "SPONSORED": "COMMANDITÉ",
        "BUMPED": "PROPULSÉ",
        "TRENDING": "TENDANCE",
        "Fixed": "Fixe",
        "Negotiable": "Négociable",
        "Contact for price": "Contacter pour le prix",
        "Private Seller": "Vendeur particulier",
        "Commercial Dealer": "Concessionnaire / Professionnel",
        "Email": "Courriel",
        "Phone": "Téléphone",
        "ID Verified": "Identité vérifiée",
        "Your Listing": "Votre annonce",
        "Active": "Active",
        "RECOMMENDED": "RECOMMANDÉ",

        // Buttons
        "Save": "Enregistrer",
        "Share": "Partager",
        "Message Seller": "Envoyer un message",
        "Promote & Boost Ad": "Promouvoir et booster",
        "Buy Now / Secure Checkout": "Acheter maintenant / Paiement sécurisé",
        "Get Directions": "Itinéraire",
        "Google Maps": "Google Maps",
        "Read Full Description": "Lire la description complète",
        "Show Less": "Afficher moins",
        "Edit Ad": "Modifier l'annonce",
        "My Listings": "Mes annonces",
        "Browse All Listings": "Parcourir toutes les annonces",

        // Location & Disclaimers
        "Near": "Près de",
        "Approximate area shown to protect seller privacy. Exact meeting location is arranged securely upon agreement.": "Zone approximative affichée pour protéger la vie privée du vendeur. Le lieu de rencontre exact est convenu en toute sécurité.",

        // Attributes (Keys & Values)
        "Condition": "État",
        "Brand New": "Neuf",
        "Like New": "Comme neuf",
        "Used - Good": "Bon état",
        "Good": "Bon état",
        "Fair": "État moyen",
        "Make": "Marque",
        "Model": "Modèle",
        "Year": "Année",
        "Mileage": "Kilométrage",
        "Transmission": "Transmission",
        "Automatic": "Automatique",
        "Manual": "Manuelle",
        "Fuel Type": "Carburant",
        "Gasoline": "Essence",
        "Diesel": "Diesel",
        "Electric": "Électrique",
        "Hybrid": "Hybride",
        "Bedrooms": "Chambres",
        "Bathrooms": "Salles de bain",
        "Size": "Superficie",
        "Furnished": "Meublé",
        "Pet Friendly": "Animaux acceptés",
        "Yes": "Oui",
        "No": "Non",
        "Available": "Disponible",

        // Safety tips
        "Meet in a safe public place:": "Rencontrez-vous dans un lieu public sécurisé :",
        "Police station exchange zones, busy transit hubs, or bank lobbies.": "Zones d'échange des postes de police, carrefours de transport fréquentés ou halls de banque.",
        "Inspect before paying:": "Inspectez avant de payer :",
        "Examine items, test electronics, and verify vehicle titles in person.": "Examinez les articles, testez les appareils électroniques et vérifiez les titres de véhicules en personne.",
        "Never send advance deposits:": "N'envoyez jamais d'acompte à l'avance :",
        "Avoid wire transfers, gift cards, or crypto payments to unverified parties.": "Évitez les virements télégraphiques, cartes-cadeaux ou paiements cryptos à des parties non vérifiées.",
        "Notice anything suspicious about this ad?": "Vous remarquez quelque chose de suspect concernant cette annonce ?",

        // Community Meetups
        "About this Meetup": "À propos de cette rencontre",
        "Date & Time": "Date et heure",
        "Expense Type": "Type de frais",
        "Free Activity": "Activité gratuite",
        "Split the Bill": "Partage de l'addition",
        "Host Pays": "L'hôte prend en charge",
        "FULL": "COMPLET",
        "OPEN": "OUVERT",
        "CANCELLED": "ANNULÉ",
        "COMPLETED": "TERMINÉ",
        "TODAY": "AUJOURD'HUI",
        "Community Meetups": "Rencontres communautaires",
        "Host a Meetup": "Organiser une rencontre",
        "Active Meetups": "Rencontres actives",
        "UPCOMING EVENTS": "ÉVÉNEMENTS À VENIR",
        "joined": "inscrits"
    }
};

// Generate reverse dictionary for English
DETAIL_PHRASES.en = {};
for (const [en, fr] of Object.entries(DETAIL_PHRASES.fr)) {
    DETAIL_PHRASES.en[fr] = en;
}

window.toggleListingTranslation = async function (btn, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    if (!btn || btn.classList.contains('is-loading')) return;

    const listingId = btn.dataset.listingId;
    const scope = btn.dataset.scope || 'card';
    const targetLocale = btn.dataset.targetLocale || 'fr';
    const labelTranslate = btn.dataset.labelTranslate || 'Translate';
    const labelOriginal = btn.dataset.labelOriginal || 'Show Original';

    // Locate target elements depending on scope
    let titleEl = null;
    let descEl = null;
    let breadcrumbEl = null;

    if (scope === 'card') {
        const card = btn.closest('.listing-card') || btn.closest('article');
        if (card) {
            titleEl = card.querySelector('.listing-title a') || card.querySelector('.listing-title');
        }
    } else if (scope === 'hero') {
        const slide = btn.closest('.swiper-slide') || btn.closest('.hero-ad-card') || btn.closest('section');
        if (slide) {
            titleEl = slide.querySelector('.hero-listing-title a') || slide.querySelector('.hero-listing-title');
            descEl = slide.querySelector('.hero-description');
        }
    } else {
        // Detail page scope - desktop sidebar title, mobile title, and description
        titleEl = document.querySelector('.sidebar-listing-title') || document.querySelector('.mobile-detail-title');
        descEl = document.getElementById('descriptionContent');
        breadcrumbEl = document.querySelector('.marketplace-breadcrumb .breadcrumb-item.active');
    }

    // Toggle back to Original if already translated
    if (btn.classList.contains('is-translated')) {
        if (titleEl && btn._originalTitle !== undefined) {
            titleEl.textContent = btn._originalTitle;
        }
        const mobileTitle = document.querySelector('.mobile-detail-title');
        if (mobileTitle && mobileTitle !== titleEl && btn._originalTitle !== undefined) {
            mobileTitle.textContent = btn._originalTitle;
        }
        if (breadcrumbEl && btn._originalBreadcrumb !== undefined) {
            breadcrumbEl.textContent = btn._originalBreadcrumb;
        }

        if (descEl && btn._originalDesc !== undefined) {
            if (scope === 'hero') {
                descEl.textContent = btn._originalDesc;
            } else {
                descEl.innerHTML = btn._originalDesc;
            }
        }

        if (scope === 'detail') {
            restoreDetailElements();
        }

        btn.classList.remove('is-translated');
        const labelSpan = btn.querySelector('.translate-btn-label');
        if (labelSpan) labelSpan.textContent = labelTranslate;
        btn.setAttribute('aria-label', labelTranslate);
        btn.setAttribute('title', labelTranslate);
        return;
    }

    // If cache in DOM memory already exists from earlier fetch
    if (btn._translatedTitle !== undefined) {
        if (titleEl) {
            btn._originalTitle = titleEl.textContent.trim();
            titleEl.textContent = btn._translatedTitle;
        }
        const mobileTitle = document.querySelector('.mobile-detail-title');
        if (mobileTitle && mobileTitle !== titleEl) {
            mobileTitle.textContent = btn._translatedTitle;
        }
        if (breadcrumbEl && btn._originalBreadcrumb !== undefined) {
            breadcrumbEl.textContent = btn._translatedTitle;
        }

        if (descEl && btn._translatedDesc !== undefined) {
            if (scope === 'hero') {
                btn._originalDesc = descEl.textContent.trim();
                descEl.textContent = btn._translatedDesc;
            } else {
                btn._originalDesc = descEl.innerHTML;
                descEl.innerHTML = btn._translatedDesc;
            }
        }

        if (scope === 'detail') {
            translateDetailElements(targetLocale);
        }

        btn.classList.add('is-translated');
        const labelSpan = btn.querySelector('.translate-btn-label');
        if (labelSpan) labelSpan.textContent = labelOriginal;
        btn.setAttribute('aria-label', labelOriginal);
        btn.setAttribute('title', labelOriginal);
        return;
    }

    // Store original text before fetching
    if (titleEl && btn._originalTitle === undefined) {
        btn._originalTitle = titleEl.textContent.trim();
    }
    if (breadcrumbEl && btn._originalBreadcrumb === undefined) {
        btn._originalBreadcrumb = breadcrumbEl.textContent.trim();
    }
    if (descEl && btn._originalDesc === undefined) {
        btn._originalDesc = (scope === 'hero') ? descEl.textContent.trim() : descEl.innerHTML;
    }

    // Start loading state
    btn.classList.add('is-loading');
    const spinner = btn.querySelector('.translate-spinner');
    const icon = btn.querySelector('.translate-icon');
    const labelSpan = btn.querySelector('.translate-btn-label');
    if (spinner) spinner.classList.remove('d-none');
    if (icon) icon.classList.add('d-none');
    if (labelSpan) labelSpan.textContent = (targetLocale === 'fr') ? 'Traduction...' : 'Translating...';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const response = await fetch(`/listings/${listingId}/translate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                target_locale: targetLocale,
                scope: scope
            })
        });

        const data = await response.json();

        if (response.ok && data.success && data.translated) {
            btn._translatedTitle = data.translated.title;
            if (titleEl && data.translated.title) {
                titleEl.textContent = data.translated.title;
            }
            const mobileTitle = document.querySelector('.mobile-detail-title');
            if (mobileTitle && mobileTitle !== titleEl && data.translated.title) {
                mobileTitle.textContent = data.translated.title;
            }
            if (breadcrumbEl && data.translated.title) {
                breadcrumbEl.textContent = data.translated.title;
            }

            if (descEl && data.translated.description) {
                if (scope === 'hero') {
                    btn._translatedDesc = data.translated.description;
                    descEl.textContent = data.translated.description;
                } else {
                    btn._translatedDesc = escapeAndFormatDesc(data.translated.description);
                    descEl.innerHTML = btn._translatedDesc;
                }
            }

            if (scope === 'detail') {
                translateDetailElements(targetLocale);
            }

            btn.classList.add('is-translated');
            if (labelSpan) labelSpan.textContent = labelOriginal;
            btn.setAttribute('aria-label', labelOriginal);
            btn.setAttribute('title', labelOriginal);
        } else {
            const errorMsg = data.message || ((targetLocale === 'fr') ? 'Traduction temporairement indisponible' : 'Translation currently unavailable');
            if (labelSpan) {
                labelSpan.textContent = errorMsg;
                setTimeout(() => {
                    labelSpan.textContent = labelTranslate;
                }, 3500);
            }
        }
    } catch (err) {
        console.error('Translation error:', err);
        if (labelSpan) {
            labelSpan.textContent = labelTranslate;
        }
    } finally {
        btn.classList.remove('is-loading');
        if (spinner) spinner.classList.add('d-none');
        if (icon) icon.classList.remove('d-none');
    }
};

/**
 * Escapes raw text and converts newlines to <br> safely.
 */
function escapeAndFormatDesc(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/\n/g, '<br>');
}

/**
 * Translates UI sections, badges, buttons, location, and attributes on listing details.
 */
function translateDetailElements(targetLocale) {
    const dictionary = DETAIL_PHRASES[targetLocale] || {};
    const selectors = [
        '.section-card-title span',
        '.section-card-title',
        '.specs-tags-label',
        '.desc-badge',
        '.location-privacy-badge',
        '.sidebar-price-badge',
        '.trust-badge',
        '.seller-type-tag',
        '.spec-pill-badge',
        '#topSaveBtn span',
        '#topShareBtn span',
        '#sidebarMessageBtn span',
        '#sidebarSaveBtn span',
        '#sidebarShareBtn span',
        '#sidebarBuyNowBtn span',
        '#toggleDescText',
        'a.btn-message-seller-primary span',
        '.btn-detail-action span',
        '.location-disclaimer',
        '.specs-attributes-grid .attr-label',
        '.specs-attributes-grid .attr-value',
        '.safety-checklist li span strong',
        '.safety-card-footer span',
        '.section-heading',
        '.section-eyebrow'
    ];

    document.querySelectorAll(selectors.join(', ')).forEach(el => {
        if (!el || el.classList.contains('translate-btn-label')) return;

        const raw = el.textContent.trim();
        if (!raw) return;

        if (el.dataset.origText === undefined) {
            el.dataset.origText = raw;
        }

        // Direct phrase match
        if (dictionary[raw]) {
            el.textContent = dictionary[raw];
            return;
        }

        // Location "Near XYZ" -> "Près de XYZ"
        if (targetLocale === 'fr' && raw.startsWith('Near ')) {
            el.textContent = 'Près de ' + raw.substring(5);
        } else if (targetLocale === 'en' && raw.startsWith('Près de ')) {
            el.textContent = 'Near ' + raw.substring(8);
        }

        // Metas: "120 views" -> "120 vues"
        if (targetLocale === 'fr' && raw.includes('views')) {
            el.textContent = raw.replace(/\bviews\b/g, 'vues');
        } else if (targetLocale === 'en' && raw.includes('vues')) {
            el.textContent = raw.replace(/\bvues\b/g, 'views');
        }

        // "reviews" -> "avis"
        if (targetLocale === 'fr' && raw.includes('reviews')) {
            el.textContent = raw.replace(/\breviews\b/g, 'avis');
        } else if (targetLocale === 'en' && raw.includes('avis')) {
            el.textContent = raw.replace(/\bavis\b/g, 'reviews');
        }
    });
}

/**
 * Restores original UI sections, badges, buttons, location, and attributes.
 */
function restoreDetailElements() {
    document.querySelectorAll('[data-orig-text]').forEach(el => {
        if (el.dataset.origText !== undefined) {
            el.textContent = el.dataset.origText;
        }
    });
}

/**
 * Toggles translation for Community Meetups (Cards or Detail Page).
 */
window.toggleMeetupTranslation = async function (btn, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    if (!btn || btn.classList.contains('is-loading')) return;

    const meetupId = btn.dataset.meetupId;
    const scope = btn.dataset.scope || 'card';
    const targetLocale = btn.dataset.targetLocale || 'fr';
    const labelTranslate = btn.dataset.labelTranslate || 'Translate';
    const labelOriginal = btn.dataset.labelOriginal || 'Show Original';

    let titleEl = null;
    let descEl = null;
    let breadcrumbEl = null;

    if (scope === 'card') {
        const card = btn.closest('.static-card') || btn.closest('article');
        if (card) {
            titleEl = card.querySelector('.static-card-title a') || card.querySelector('.static-card-title');
            descEl = card.querySelector('.static-card-text');
        }
    } else {
        // Detail page scope
        titleEl = document.querySelector('.static-hero-title') || document.querySelector('h1');
        descEl = document.querySelector('.section-subtext');
        breadcrumbEl = document.querySelector('.breadcrumb .breadcrumb-item.active');
    }

    // Toggle back to Original if already translated
    if (btn.classList.contains('is-translated')) {
        if (titleEl && btn._originalTitle !== undefined) {
            titleEl.textContent = btn._originalTitle;
        }
        if (descEl && btn._originalDesc !== undefined) {
            descEl.innerHTML = btn._originalDesc;
        }
        if (breadcrumbEl && btn._originalBreadcrumb !== undefined) {
            breadcrumbEl.textContent = btn._originalBreadcrumb;
        }

        if (scope === 'detail') {
            restoreDetailElements();
        }

        btn.classList.remove('is-translated');
        const labelSpan = btn.querySelector('.translate-btn-label');
        if (labelSpan) labelSpan.textContent = labelTranslate;
        btn.setAttribute('aria-label', labelTranslate);
        btn.setAttribute('title', labelTranslate);
        return;
    }

    // Cache original text
    if (titleEl && btn._originalTitle === undefined) {
        btn._originalTitle = titleEl.textContent.trim();
    }
    if (descEl && btn._originalDesc === undefined) {
        btn._originalDesc = descEl.innerHTML;
    }
    if (breadcrumbEl && btn._originalBreadcrumb === undefined) {
        btn._originalBreadcrumb = breadcrumbEl.textContent.trim();
    }

    // Start loading state
    btn.classList.add('is-loading');
    const spinner = btn.querySelector('.translate-spinner');
    const icon = btn.querySelector('.translate-icon');
    const labelSpan = btn.querySelector('.translate-btn-label');
    if (spinner) spinner.classList.remove('d-none');
    if (icon) icon.classList.add('d-none');
    if (labelSpan) labelSpan.textContent = (targetLocale === 'fr') ? 'Traduction...' : 'Translating...';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const response = await fetch(`/community/${meetupId}/translate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                target_locale: targetLocale,
                scope: scope
            })
        });

        const data = await response.json();

        if (response.ok && data.success && data.translated) {
            btn._translatedTitle = data.translated.title;
            if (titleEl && data.translated.title) {
                titleEl.textContent = data.translated.title;
            }
            if (breadcrumbEl && data.translated.title) {
                breadcrumbEl.textContent = data.translated.title;
            }
            if (descEl && data.translated.description) {
                btn._translatedDesc = escapeAndFormatDesc(data.translated.description);
                descEl.innerHTML = btn._translatedDesc;
            }

            if (scope === 'detail') {
                translateDetailElements(targetLocale);
            }

            btn.classList.add('is-translated');
            if (labelSpan) labelSpan.textContent = labelOriginal;
            btn.setAttribute('aria-label', labelOriginal);
            btn.setAttribute('title', labelOriginal);
        } else {
            const errorMsg = data.message || ((targetLocale === 'fr') ? 'Traduction temporairement indisponible' : 'Translation currently unavailable');
            if (labelSpan) {
                labelSpan.textContent = errorMsg;
                setTimeout(() => {
                    labelSpan.textContent = labelTranslate;
                }, 3500);
            }
        }
    } catch (err) {
        console.error('Meetup translation error:', err);
        if (labelSpan) labelSpan.textContent = labelTranslate;
    } finally {
        btn.classList.remove('is-loading');
        if (spinner) spinner.classList.add('d-none');
        if (icon) icon.classList.remove('d-none');
    }
};

/**
 * Auto-translation initializer when locale is French.
 */
async function initAutoTranslation() {
    const htmlLang = document.documentElement.lang ? document.documentElement.lang.toLowerCase() : '';
    const isFrench = htmlLang.startsWith('fr');
    if (!isFrench) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // 1. Listing Detail Auto-Translate
    const detailBtn = document.querySelector('.btn-listing-translate[data-scope="detail"]');
    if (detailBtn && !detailBtn.classList.contains('is-translated') && !detailBtn.classList.contains('is-loading')) {
        window.toggleListingTranslation(detailBtn);
    }

    // 2. Listing Cards Batch Auto-Translate
    const cardBtns = Array.from(document.querySelectorAll('.btn-listing-translate[data-scope="card"]:not(.is-translated):not(.is-loading)'));
    if (cardBtns.length > 0) {
        const idMap = new Map();
        cardBtns.forEach(btn => {
            const id = parseInt(btn.dataset.listingId, 10);
            if (id && !isNaN(id)) {
                if (!idMap.has(id)) {
                    idMap.set(id, []);
                }
                idMap.get(id).push(btn);
            }
        });

        const listingIds = Array.from(idMap.keys());
        if (listingIds.length > 0) {
            try {
                const response = await fetch('/listings/translate-batch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        listing_ids: listingIds,
                        target_locale: 'fr'
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data && data.success && data.translations) {
                        Object.entries(data.translations).forEach(([idStr, trans]) => {
                            const btns = idMap.get(parseInt(idStr, 10)) || [];
                            if (!trans || !trans.title) return;

                            btns.forEach(btn => {
                                const card = btn.closest('.listing-card') || btn.closest('article');
                                const titleEl = card ? (card.querySelector('.listing-title a') || card.querySelector('.listing-title')) : null;

                                if (titleEl) {
                                    if (btn._originalTitle === undefined) {
                                        btn._originalTitle = titleEl.textContent.trim();
                                    }
                                    btn._translatedTitle = trans.title;
                                    titleEl.textContent = trans.title;

                                    btn.classList.add('is-translated');
                                    const labelSpan = btn.querySelector('.translate-btn-label');
                                    const labelOriginal = btn.dataset.labelOriginal || "Voir l'original";
                                    if (labelSpan) labelSpan.textContent = labelOriginal;
                                    btn.setAttribute('aria-label', labelOriginal);
                                    btn.setAttribute('title', labelOriginal);
                                }
                            });
                        });
                    }
                }
            } catch (err) {
                console.warn('Batch translation error:', err);
            }
        }
    }

    // 3. Hero Carousel Batch Auto-Translate
    const heroBtns = Array.from(document.querySelectorAll('.btn-listing-translate[data-scope="hero"]:not(.is-translated):not(.is-loading)'));
    if (heroBtns.length > 0) {
        const heroIdMap = new Map();
        heroBtns.forEach(btn => {
            const id = parseInt(btn.dataset.listingId, 10);
            if (id && !isNaN(id)) {
                if (!heroIdMap.has(id)) {
                    heroIdMap.set(id, []);
                }
                heroIdMap.get(id).push(btn);
            }
        });

        const heroListingIds = Array.from(heroIdMap.keys());
        if (heroListingIds.length > 0) {
            try {
                const response = await fetch('/listings/translate-batch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        listing_ids: heroListingIds,
                        target_locale: 'fr',
                        scope: 'hero'
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data && data.success && data.translations) {
                        Object.entries(data.translations).forEach(([idStr, trans]) => {
                            const btns = heroIdMap.get(parseInt(idStr, 10)) || [];
                            if (!trans) return;

                            btns.forEach(btn => {
                                const slide = btn.closest('.swiper-slide') || btn.closest('.hero-ad-card') || btn.closest('section');
                                const titleEl = slide ? (slide.querySelector('.hero-listing-title a') || slide.querySelector('.hero-listing-title')) : null;
                                const descEl = slide ? slide.querySelector('.hero-description') : null;

                                if (titleEl && trans.title) {
                                    if (btn._originalTitle === undefined) {
                                        btn._originalTitle = titleEl.textContent.trim();
                                    }
                                    btn._translatedTitle = trans.title;
                                    titleEl.textContent = trans.title;
                                }

                                if (descEl && trans.description) {
                                    if (btn._originalDesc === undefined) {
                                        btn._originalDesc = descEl.textContent.trim();
                                    }
                                    btn._translatedDesc = trans.description;
                                    descEl.textContent = trans.description;
                                }

                                btn.classList.add('is-translated');
                                const labelSpan = btn.querySelector('.translate-btn-label');
                                const labelOriginal = btn.dataset.labelOriginal || "Voir l'original";
                                if (labelSpan) labelSpan.textContent = labelOriginal;
                                btn.setAttribute('aria-label', labelOriginal);
                                btn.setAttribute('title', labelOriginal);
                            });
                        });
                    }
                }
            } catch (err) {
                console.warn('Hero batch translation error:', err);
            }
        }
    }

    // 4. Community Meetup Detail Auto-Translate
    const meetupDetailBtn = document.querySelector('.btn-community-translate[data-scope="detail"]');
    if (meetupDetailBtn && !meetupDetailBtn.classList.contains('is-translated') && !meetupDetailBtn.classList.contains('is-loading')) {
        window.toggleMeetupTranslation(meetupDetailBtn);
    }

    // 5. Community Meetup Cards Batch Auto-Translate
    const meetupCardBtns = Array.from(document.querySelectorAll('.btn-community-translate[data-scope="card"]:not(.is-translated):not(.is-loading)'));
    if (meetupCardBtns.length > 0) {
        const meetupIdMap = new Map();
        meetupCardBtns.forEach(btn => {
            const id = parseInt(btn.dataset.meetupId, 10);
            if (id && !isNaN(id)) {
                if (!meetupIdMap.has(id)) {
                    meetupIdMap.set(id, []);
                }
                meetupIdMap.get(id).push(btn);
            }
        });

        const meetupIds = Array.from(meetupIdMap.keys());
        if (meetupIds.length > 0) {
            try {
                const response = await fetch('/community/translate-batch', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        meetup_ids: meetupIds,
                        target_locale: 'fr'
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data && data.success && data.translations) {
                        Object.entries(data.translations).forEach(([idStr, trans]) => {
                            const btns = meetupIdMap.get(parseInt(idStr, 10)) || [];
                            if (!trans || !trans.title) return;

                            btns.forEach(btn => {
                                const card = btn.closest('.static-card') || btn.closest('article');
                                const titleEl = card ? (card.querySelector('.static-card-title a') || card.querySelector('.static-card-title')) : null;

                                if (titleEl) {
                                    if (btn._originalTitle === undefined) {
                                        btn._originalTitle = titleEl.textContent.trim();
                                    }
                                    btn._translatedTitle = trans.title;
                                    titleEl.textContent = trans.title;

                                    btn.classList.add('is-translated');
                                    const labelSpan = btn.querySelector('.translate-btn-label');
                                    const labelOriginal = btn.dataset.labelOriginal || "Voir l'original";
                                    if (labelSpan) labelSpan.textContent = labelOriginal;
                                    btn.setAttribute('aria-label', labelOriginal);
                                    btn.setAttribute('title', labelOriginal);
                                }
                            });
                        });
                    }
                }
            } catch (err) {
                console.warn('Community batch translation error:', err);
            }
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAutoTranslation);
} else {
    initAutoTranslation();
}

