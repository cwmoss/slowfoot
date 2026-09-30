export default class PreviewTooltip extends HTMLElement {
    constructor() {
        super();
        // Shadow DOM für Kapselung von Styles und Struktur erstellen
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        const imgSrc = this.getAttribute('img-src') || '';

        // HTML und CSS der Komponente definieren
        this.shadowRoot.innerHTML = `
          <style>
            :host {
              position: relative;
              display: inline-block;
              cursor: pointer;
            }
            
            .tooltip-box {
              position: absolute;
              bottom: 125%;
              left: 50%;
              transform: translateX(-50%);
              background: #fff;
              border: 1px solid #ccc;
              border-radius: 8px;
              padding: 6px;
              box-shadow: 0 4px 15px rgba(0,0,0,0.15);
              display: none;
              z-index: 10;
              line-height: 0; /* Verhindert Whitespace unter dem Bild */
            }

            .tooltip-box img {
              max-width: 200px;
              max-height: 150px;
              border-radius: 4px;
              display: block;
            }

            /* Tooltip-Pfeil unten */
            .tooltip-box::after {
              content: "";
              position: absolute;
              top: 100%;
              left: 50%;
              margin-left: -5px;
              border-width: 5px;
              border-style: solid;
              border-color: #fff transparent transparent transparent;
            }

            /* Event: Hover zeigt Tooltip */
            :host(:hover) .tooltip-box {
              display: block;
            }
          </style>
          
          <!-- Sichtbarer Text/Inhalt im Dokument -->
          <slot></slot>
          
          <!-- Das versteckte Vorschaubild -->
          <div class="tooltip-box">
            <img src="${imgSrc}" alt="Vorschau">
          </div>
        `;
    }
}

// Registrierung des Custom Elements
customElements.define('preview-tooltip', PreviewTooltip);
