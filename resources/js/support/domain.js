/**
 * Split a hostname into its name and top-level domain, for the header wordmark:
 * "www.pooriajahedi.ir" -> { name: "pooriajahedi", tld: ".ir" }.
 */
export const splitHostname = (hostname) => {
    const host = String(hostname ?? '')
        .trim()
        .toLowerCase()
        .replace(/^www\./, '');
    const dot = host.lastIndexOf('.');

    return dot > 0 ? { name: host.slice(0, dot), tld: host.slice(dot) } : { name: host, tld: '' };
};
