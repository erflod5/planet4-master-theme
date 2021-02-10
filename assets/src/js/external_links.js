export const setupExternalLinks = () => {
  const siteURL = window.location.host;

  const page = document.querySelector('.page-template');
  const article = document.getElementsByTagName('article')[0];

  let links = [];

  if (page) {
    links = links.concat([...page.querySelectorAll('a:not(.btn):not(.cover-card-heading)')]);
  }

  if (article) {
    links = links.concat([...article.querySelectorAll('a:not(.btn):not(.cover-card-heading)')]);
  }

  if (links.length > 0) {
    links.forEach(link => {
      const href = link.href || '';
      if (href && !href.includes(siteURL)) {
        const text = link.textContent || link.innerText;
        if (text.trim().length === 0) {
          return;
        }

        if (!['/', '#'].includes(href.charAt(0)) && !href.endsWith('.pdf') && !href.startsWith('javascript:')) {
          link.target = '_blank';
          link.classList.add('external-link');
        }
      }
    });
  }
};
