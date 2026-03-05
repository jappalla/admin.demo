# Link Esterni — Demo Admin Dashboard

Elenco dei file HTML contenenti link esterni con percorso del file sorgente.

---

## Link globali

Presenti in **tutti i 55 file HTML** con sidebar (`<head>` + footer/welcome-modal):

| URL | Tipo | Percorso file |
|-----|------|---------------|
| `https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap` | CSS CDN | `demo/admin/*.html` — `<head>` |
| `https://www.googletagmanager.com/gtag/js?id=G-GBZ3SGGX85` | Script | `demo/admin/*.html` — `<head>` |
| `https://www.googletagmanager.com/gtm.js` (+ `?id=GTM-NXZMQSS`) | Script inline | `demo/admin/*.html` — `<head>` |
| `https://www.googletagmanager.com/ns.html?id=GTM-NXZMQSS` | iframe noscript | `demo/admin/*.html` — prima di `</body>` |
| `https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2973766580778258` | Script | `demo/admin/*.html` — `<head>` |
| `https://embed.lottiefiles.com/animation/31548` | iframe | `demo/admin/*.html` — welcome-modal |
| `https://buttons.github.io/buttons.js` | Script | `demo/admin/*.html` — welcome-modal |
| `https://github.com/dropways/deskapp` | `<a href>` | `demo/admin/*.html` — welcome-modal (Star / Fork) |
| `https://github.com/dropways/deskapp/fork` | `<a href>` | `demo/admin/*.html` — welcome-modal |

## Link navbar (55 file con header)

| URL | Tipo | Percorso file |
|-----|------|---------------|
| `/demo/` | `<a href>` button | `demo/admin/*.html` — `.header-right` |
| `https://github.com/jappalla` | `<a href>` | `demo/admin/index.html` — `.github-link` |
| `https://github.com/dropways/deskapp` | `<a href>` | `demo/admin/*.html` (esclusi index/index2/index3) — `.github-link` |

## Link sidebar "testscript.info" (55 file con sidebar)

| URL | Tipo | Percorso file |
|-----|------|---------------|
| `https://testscript.info/` | `<a href>` | `demo/admin/*.html` — sidebar dropdown "testscript.info" |
| `https://testscript.info/app/` | `<a href>` | `demo/admin/*.html` — sidebar dropdown "testscript.info" |
| `https://testscript.info/demo/` | `<a href>` | `demo/admin/*.html` — sidebar dropdown "testscript.info" |
| `https://testscript.info/dashboard/` | `<a href>` | `demo/admin/*.html` — sidebar dropdown "testscript.info" |
| `https://developer.testscript.info/` | `<a href>` | `demo/admin/*.html` — sidebar dropdown "testscript.info" |
| `https://testscript.info/app/about` | `<a href>` | `demo/admin/*.html` — sidebar dropdown "testscript.info" |
| `https://testscript.info/app/contact` | `<a href>` | `demo/admin/*.html` — sidebar dropdown "testscript.info" |

## Link sidebar "Home" (solo alcuni file)

| URL | Tipo | Percorso file |
|-----|------|---------------|
| `http://localhost/wp/` | `<a href>` | `demo/admin/index.html` — sidebar "Home" submenu |
| `https://testscript.net/wp/` | `<a href>` | `demo/admin/index.html` — sidebar "Home" submenu |
| `http://localhost/phpMyAdmin` | `<a href>` | `demo/admin/index.html` — sidebar "Home" submenu |
| `http://localhost/wp/` | `<a href>` | `demo/admin/gallery.html` — sidebar "Home" submenu |
| `https://testscript.net/wp/` | `<a href>` | `demo/admin/gallery.html` — sidebar "Home" submenu |
| `http://localhost/phpMyAdmin` | `<a href>` | `demo/admin/gallery.html` — sidebar "Home" submenu |

---

## Link specifici per file

### `demo/admin/index.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://github.com/jappalla` | `<a href>` | `.github-link` (navbar) |
| `https://www.kacinka.it` | `<a href>` | footer `.footer-wrap` |

### `demo/admin/index2.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://github.com/jappalla` | `<a href>` | `.github-link` (navbar) |
| `https://www.kacinka.it` | `<a href>` | footer `.footer-wrap` |

### `demo/admin/index3.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://github.com/jappalla` | `<a href>` | `.github-link` (navbar) |

### `demo/admin/highchart.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://code.highcharts.com/highcharts-3d.js` | `<script src>` | prima di `</body>` |

### `demo/admin/video-player.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.mp4` | `<source src>` | `<video>` player |
| `https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.webm` | `<source src>` | `<video>` player |
| `https://cdn.plyr.io/static/demo/Kishi_Bashi_-_It_All_Began_With_a_Burst.mp3` | `<source src>` | `<audio>` player |
| `https://cdn.plyr.io/static/demo/Kishi_Bashi_-_It_All_Began_With_a_Burst.ogg` | `<source src>` | `<audio>` player |
| `https://cdn.shr.one/1.0.1/shr.js` | `<script src>` | prima di `</body>` |
| `https://github.com/sampotts/plyr` | `<a href>` | sezione Plyr info |

### `demo/admin/datatable.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://datatables.net/` | `<a href>` | credit link nel contenuto |

### `demo/admin/font-awesome.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://fontawesome.com/v4.7.0/examples/#animated` | `<a href>` | sezione animated icons |

### `demo/admin/getting-started.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://nodejs.org/en/download/` | `<a href>` | sezione prerequisiti |

### `demo/admin/introduction.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://github.com/dropways/deskapp/issues` | `<a href>` | sezione bug report |

### `demo/admin/third-party-plugins.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://getbootstrap.com/` | `<a href>` | tabella plugin |
| `https://apexcharts.com/` | `<a href>` | tabella plugin |
| `https://datatables.net/` | `<a href>` | tabella plugin |
| `https://fullcalendar.io/` | `<a href>` | tabella plugin |
| `https://www.highcharts.com/` | `<a href>` | tabella plugin |
| `https://sweetalert2.github.io/` | `<a href>` | tabella plugin |
| `https://select2.github.io` | `<a href>` | tabella plugin |
| `https://plyr.io/` | `<a href>` | tabella plugin |
| `https://www.dropzonejs.com/` | `<a href>` | tabella plugin |
| `https://fengyuanchen.github.io/cropperjs/` | `<a href>` | tabella plugin |
| `https://developer.snapappointments.com/bootstrap-select/` | `<a href>` | tabella plugin |
| `https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/` | `<a href>` | tabella plugin |
| `https://www.virtuosoft.eu/code/bootstrap-touchspin/` | `<a href>` | tabella plugin |
| `https://kenwheeler.github.io/slick/` | `<a href>` | tabella plugin |
| `https://highlightjs.org/` | `<a href>` | tabella plugin |
| `https://abpetkov.github.io/switchery/` | `<a href>` | tabella plugin |
| `https://jvectormap.com/` | `<a href>` | tabella plugin |
| `https://jhollingworth.github.io/bootstrap-wysihtml5/` | `<a href>` | tabella plugin |
| `https://github.com/xing/wysihtml5` | `<a href>` | tabella plugin |
| `https://github.com/thecreation/jquery-asColorPicker` | `<a href>` | tabella plugin |
| `https://github.com/thecreation/jquery-asColor` | `<a href>` | tabella plugin |
| `https://github.com/thecreation/jquery-asGradient` | `<a href>` | tabella plugin |
| `https://felicegattuso.com/projects/timedropper/` | `<a href>` | tabella plugin |
| `http://anthonyterrien.com/knob/` | `<a href>` | tabella plugin |
| `http://fancyapps.com/fancybox/` | `<a href>` | tabella plugin |
| `http://ionden.com/a/plugins/ion.rangeSlider/index.html` | `<a href>` | tabella plugin |
| `http://manos.malihu.gr/jquery-custom-content-scroller/` | `<a href>` | tabella plugin |
| `http://t1m0n.name/air-datepicker/docs/` | `<a href>` | tabella plugin |
| `http://www.jquery-steps.com` | `<a href>` | tabella plugin |

### `demo/admin/src/plugins/bootstrap-select/test.html`
| URL | Tipo | Posizione nel file |
|-----|------|-------------------|
| `https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css` | `<link href>` | `<head>` |
| `https://code.jquery.com/jquery-3.2.1.slim.js` | `<script src>` | prima di `</body>` |
| `https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js` | `<script src>` | prima di `</body>` |

---

## Riepilogo

- **55 file HTML** con layout completo (sidebar + header) — percorso: `demo/admin/*.html`
- **8 file** senza sidebar: `demo/admin/400.html`, `403.html`, `404.html`, `500.html`, `503.html`, `login.html`, `forgot-password.html`, `register.html`, `reset-password.html`
- **1 file** in sottocartella: `demo/admin/src/plugins/bootstrap-select/test.html`
- **Link CDN globali** (`<head>` di ogni file): Google Fonts, Google Analytics/GTM, Google AdSense
- **Link iframe** (welcome-modal): Lottie, GitHub Buttons
- **Link navigazione** (navbar/sidebar): GitHub jappalla, testscript.info (7 pagine), Demo Hub
