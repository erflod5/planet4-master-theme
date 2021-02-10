// Add pdf icon to pdf links
export const setupPDFIcon = () => {
  const links = [...document.querySelectorAll('a[href*=".pdf"]')];

  links.forEach(link => {
    const linkParent = link.parentElement.nodeName;
    if (['H1', 'H2', 'H3', 'H4', 'H5', 'H6'].includes(linkParent)) {
      return;
    }

    const linkChildren = [...link.childNodes];
    const isImage = linkChildren.find(linkChild => linkChild.nodeName === 'IMG');

    if (isImage) {
      return;
    } else {
      link.classList.add('pdf-link');
    }
  });
};
