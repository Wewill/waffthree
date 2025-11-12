# Améliorations main.js - Plan d'action

> **Date:** 2025-11-12
> **Fichier concerné:** `dist/js/theme/main.js`
> **Objectif:** Améliorer le code sans changer les comportements

---

## 📋 Stratégie de déploiement

### Approche progressive et sécurisée

1. ✅ **Créer une branche Git dédiée** (pour pouvoir rollback facilement)
2. ✅ **Créer un fichier de backup** (`main.backup.js`)
3. ✅ **Créer un document de test** listant tous les comportements à vérifier
4. ✅ **Appliquer les changements par phases** (chaque phase = 1 commit)
5. ✅ **Tester après chaque phase** avant de passer à la suivante

---

## 🔴 Phase 1 - Corrections critiques (BUGS)

### Bug 1: Comparaison booléen/string (lignes 144, 176)
**Problème:** On compare un booléen à une string `'true'`

```javascript
// ❌ AVANT (ligne 144)
otherToggle.checked = newState === 'true';

// ✅ APRÈS
otherToggle.checked = newState;
```

```javascript
// ❌ AVANT (ligne 176)
toggle.checked = state === 'true';

// ✅ APRÈS
toggle.checked = state === 'true'; // OK car state est une string ici
```

### Bug 2: Variable `time` non déclarée (ligne 847)
**Problème:** Variable globale non intentionnelle

```javascript
// ❌ AVANT
time = setTimeout(

// ✅ APRÈS
let time = setTimeout(
```

### Bug 3: Condition impossible (ligne 865)
**Problème:** `_h < 0` est impossible car `_h` est une longueur

```javascript
// ❌ AVANT
if (expanded === false && _h < 0) {

// ✅ APRÈS
if (expanded === false && _h === 0) {
```

### Bug 4: Typo commentaire (ligne 283)
```javascript
// ❌ AVANT: aremovedd
// ✅ APRÈS: removed
```

**Test après Phase 1:**
- Vérifier le toggle "programmation favorited"
- Vérifier le menu collapse/expand au hover
- Vérifier qu'aucune erreur console n'apparaît

---

## 🟡 Phase 2 - Modernisation du code

### Conversion `var` → `const`/`let`

**Règle:**
- `const` pour les valeurs qui ne changent pas
- `let` pour les valeurs qui changent

```javascript
// Ligne 85-97 (Tooltips/Popovers)
// ❌ AVANT
var tooltipTriggerList = [].slice.call(
var tooltipList = tooltipTriggerList.map(

// ✅ APRÈS
const tooltipTriggerList = [].slice.call(
const tooltipList = tooltipTriggerList.map(
```

**Liste complète des `var` à convertir:**
- Ligne 85: `var tooltipTriggerList` → `const`
- Ligne 88: `var tooltipList` → `const`
- Ligne 92: `var popoverTriggerList` → `const`
- Ligne 95: `var popoverList` → `const`
- Ligne 227: `var fitEditionBadge` → `const`
- Ligne 254: `var _offcanvas` → `const`
- Ligne 258: `var _navbarToggleExternalContent` → `const`
- Ligne 294-297: `var nav, link, scroll, offset` → `const`
- Ligne 352: `var toggleAffix` → `const`
- Ligne 368: `var ele, wrapper` → `const/let`
- Ligne 372: `var wrapper` → `const` (redéclaration!)
- Ligne 389: `var fixVH` → `const`
- Ligne 416-419: `var ele, src, trg` → `const`
- Ligne 431-434: `var ele, src, trg` → `const`
- Ligne 709-711: `var $card, lastCard, running` → `const/let`
- Ligne 759: `var cardsAutoPlay` → `const`
- Ligne 940: `var $t` → `const`

### Cohérence jQuery/Vanilla JS

**Objectif:** Garder jQuery là où c'est déjà utilisé, utiliser vanilla JS pour le nouveau code

```javascript
// Exemple ligne 808-820 : Bon usage (vanilla JS moderne)
jQuery("#navbarToggleExternalContent .row").on("mouseenter", function() {
  isInMenuZone = true;
  // ...
})
```

**Test après Phase 2:**
- Tester toutes les fonctionnalités listées dans la checklist
- Vérifier qu'aucune régression n'est apparue

---

## 🟢 Phase 3 - Refactoring (Code dupliqué)

### 3.1 Factoriser fixVH (lignes 415-444)

**Avant:** Code dupliqué pour vh-50 et vh-100

```javascript
// ✅ APRÈS - Version factorisée
function initFixVH(className, sourceClass, targetClass) {
  jQuery(className).each(function () {
    const ele = jQuery(this);
    const src = jQuery(this).find(sourceClass);
    const trg = jQuery(this).find(targetClass);

    jQuery(window).on("resize", function () {
      fixVH(ele, src, trg, "resize");
    });

    setTimeout(function () {
      fixVH(ele, src, trg, "init");
    }, 100);
  });
}

// Utilisation
initFixVH(".fix-vh-50", ".min-h-50", ".vh-50");
initFixVH(".fix-vh-100", ".min-h-100", ".vh-100");
```

### 3.2 Factoriser Stacked Cards (lignes 713-782)

**Avant:** Logique `prependList` dupliquée

```javascript
// ✅ APRÈS - Version factorisée
function prependStackedCard() {
  if (jQuery(".stacked-cards .card").hasClass("activeNow")) {
    const $slicedCard = jQuery(".stacked-cards .card")
      .slice(lastCard)
      .removeClass("transformThis activeNow");
    jQuery(".stacked-cards ul").prepend($slicedCard);
    running = false;
  }
}

function nextStackedCard() {
  running = true;
  jQuery(".stacked-cards li")
    .last()
    .removeClass("transformPrev")
    .addClass("transformThis")
    .prev()
    .addClass("activeNow");
  setTimeout(prependStackedCard, 150);
}

// Utilisation
jQuery("#stacked-cards-next").click(nextStackedCard);
```

### 3.3 Magic numbers → Constantes

```javascript
// ✅ En haut du fichier, après les VH calculations
const CONFIG = {
  SCROLL_OFFSET: 72.015625,
  MODAL_RELOAD_DELAY: 350,
  STACKED_CARDS_DELAY: 150,
  STACKED_CARDS_AUTOPLAY: 3000,
  MENU_COLLAPSE_DELAY: 3000,
  MENU_ZONE_DELAY: 100,
  MENU_HOVER_DELAY: 10,
};

// Utilisation
var offset = CONFIG.SCROLL_OFFSET; // ligne 297
setTimeout(() => { window.location.reload(); }, CONFIG.MODAL_RELOAD_DELAY); // ligne 153
```

**Test après Phase 3:**
- Vérifier fixVH sur mobile et desktop
- Vérifier les stacked cards (navigation et autoplay)
- Vérifier le scroll spy de la modal programmation

---

## 🔵 Phase 4 - Nettoyage

### 4.1 Console.logs conditionnels

```javascript
// ✅ Ajouter en haut du fichier
const DEBUG_MODE = false; // Mettre à true pour activer les logs

function debugLog(...args) {
  if (DEBUG_MODE) {
    console.log(...args);
  }
}

// Remplacer tous les console.log par debugLog
debugLog("#Dom is ready");
debugLog("#LAZY : afterLoad");
// etc...
```

### 4.2 Cookie sécurisé (ligne 150, 168)

```javascript
// ❌ AVANT
document.cookie = `programmation-modal-favorited=${newState}; path=/; max-age=31536000`;

// ✅ APRÈS
document.cookie = `programmation-modal-favorited=${newState}; path=/; max-age=31536000; SameSite=Lax`;
// Note: secure flag uniquement en HTTPS
```

### 4.3 Nettoyer le code commenté

Supprimer ou documenter les blocs commentés inutiles:
- Lignes 35, 82-83, 99-102
- Lignes 254-256, 269-272, 283
- Lignes 671-700
- Lignes 927-936
- Lignes 987-995
- Lignes 1003-1010

**Test après Phase 4:**
- Vérifier que les cookies fonctionnent toujours
- Test complet de toutes les fonctionnalités

---

## ✅ Checklist de test complète

### Navigation & Menu
- [ ] Menu principal s'ouvre/ferme correctement
- [ ] Sous-menus s'affichent au hover
- [ ] Sous-menus se ferment correctement
- [ ] Classe `navbar-external-open/close` appliquée au body
- [ ] Classe `navbar-affix` appliquée au scroll
- [ ] Navigation responsive (mobile/tablet/desktop)
- [ ] Icônes "down-right" affichées sur items avec sous-menu

### Sliders (Slick)
- [ ] Home slide fonctionne (autoplay + navigation)
- [ ] Slider carousel fonctionne
- [ ] Slider samedays fonctionne (4 slides desktop)
- [ ] Slider partners fonctionne (15 slides desktop)
- [ ] Flash slider fonctionne
- [ ] Film card slider fonctionne
- [ ] Farm slide fonctionne
- [ ] Navigation entre slides manuelle OK
- [ ] Responsive des sliders OK

### Modal Programmation
- [ ] Modal s'ouvre via `.toggle-programmation`
- [ ] Toggle "favorited" fonctionne
- [ ] État "favorited" persisté (localStorage + cookie)
- [ ] Scroll spy fonctionne (navigation interne)
- [ ] Navigation vers sections fonctionne
- [ ] Liens `rel="open-favorite-prog"` fonctionnent
- [ ] Liens `rel="open-full-prog"` fonctionnent
- [ ] Reload après changement de toggle OK
- [ ] Synchronisation multi-toggles OK

### Images & Animations
- [ ] Lazy loading fonctionne
- [ ] Images s'affichent avec fadeIn
- [ ] AOS animations fonctionnent
- [ ] Delayed anchors AOS après images chargées
- [ ] Erreurs lazy loading loggées (si DEBUG_MODE)

### Responsive & VH
- [ ] VH calculations correctes (mobile iOS)
- [ ] --vh, --vh40, --vh50, --vh60, --vh100 définies
- [ ] Fix VH-50 fonctionne
- [ ] Fix VH-100 fonctionne
- [ ] Resize window recalcule correctement

### Stacked Cards
- [ ] Cartes empilées s'affichent
- [ ] Bouton "next" fonctionne
- [ ] Bouton "prev" fonctionne
- [ ] Autoplay fonctionne (3s)
- [ ] Animations smooth (150ms)

### Autres fonctionnalités
- [ ] Tooltips Bootstrap 5 fonctionnent
- [ ] Popovers Bootstrap 5 fonctionnent
- [ ] Fitty (edition badge) fonctionne
- [ ] Collapse-hover fonctionnent

### Tests techniques
- [ ] Aucune erreur console
- [ ] Aucun warning console
- [ ] Pas de memory leaks (long usage)
- [ ] Performance correcte (pas de lag)
- [ ] Compatible tous navigateurs (Chrome, Firefox, Safari, Edge)

---

## 🚀 Commandes Git pour exécuter le plan

```bash
# 1. Créer la branche
git checkout -b improvement/main-js-refactoring

# 2. Créer le backup
cp dist/js/theme/main.js dist/js/theme/main.backup.js

# 3. Après chaque phase
git add dist/js/theme/main.js
git commit -m "Phase X: [description]"

# 4. Tester en local ou sur environnement de staging

# 5. Si tout OK, merger dans dev
git checkout dev
git merge improvement/main-js-refactoring

# 6. Si problème, revenir en arrière
git checkout dev
git reset --hard HEAD~1  # ou identifier le commit
```

---

## 📞 Pour déclencher ces améliorations plus tard

**Option 1 - Demander à Claude Code:**
```
"Applique le plan d'amélioration du fichier IMPROVEMENTS_MAIN_JS.md,
en suivant phase par phase avec tests entre chaque phase"
```

**Option 2 - Manuellement:**
1. Lire ce document
2. Créer la branche Git
3. Appliquer phase 1, tester, commiter
4. Appliquer phase 2, tester, commiter
5. etc.

---

## 📊 Estimation temps

- **Phase 1 (Bugs):** ~15 min code + 30 min tests = 45 min
- **Phase 2 (Modernisation):** ~30 min code + 45 min tests = 1h15
- **Phase 3 (Refactoring):** ~1h code + 1h tests = 2h
- **Phase 4 (Nettoyage):** ~30 min code + 30 min tests = 1h

**Total estimé: ~5h** (à répartir sur plusieurs sessions)

---

## ⚠️ Points d'attention

1. **Ne pas tout faire d'un coup** - Respecter les phases
2. **Tester systématiquement** après chaque phase
3. **Avoir un environnement de staging** pour tester avant prod
4. **Garder le backup** jusqu'à validation complète en production
5. **Documenter les problèmes** rencontrés pendant les tests

---

## 🔄 Rollback rapide si problème

```bash
# Si problème détecté
git checkout dev
git revert <commit-hash>  # Revenir au commit précédent

# OU restaurer le backup
cp dist/js/theme/main.backup.js dist/js/theme/main.js
```

---

**Document créé par Claude Code - Prêt à être exécuté**
