/**
 * How each skill icon is presented in the skills section: its brand colour
 * tints the tile, and logos with a black body get a thin light outline so they
 * survive the dark theme. Icons that are not listed use the site's accent.
 */

const BRAND_COLORS = {
    'logos:javascript': '#f7df1e',
    'logos:typescript-icon': '#3178c6',
    'logos:vue': '#42b883',
    'logos:react': '#00d8ff',
    'logos:nextjs-icon': '#9ca3af',
    'logos:tailwindcss-icon': '#38bdf8',
    'logos:bootstrap': '#7e13f8',
    'logos:laravel': '#ff2d20',
    'logos:php': '#777bb4',
    'logos:nodejs-icon': '#539e43',
    'logos:mysql': '#00758f',
    'logos:postgresql': '#336791',
    'logos:mongodb-icon': '#47a248',
    'logos:redis': '#dc382d',
    'logos:git-icon': '#f05032',
    'logos:docker-icon': '#2396ed',
    'logos:linux-tux': '#fcc624',
    'logos:figma': '#a259ff',
    'logos:postman-icon': '#ff6c37',
    'mdi:toolbox-outline': '#e1421f',
};

const OUTLINED_ICONS = new Set(['logos:nextjs-icon', 'logos:linux-tux']);

/** The icon's brand colour, or null when it has none on record. */
export const brandColor = (icon) => BRAND_COLORS[icon] ?? null;

/** Whether the logo needs the light outline in the dark theme. */
export const isOutlined = (icon) => OUTLINED_ICONS.has(icon);
