(function (Drupal, once) {
  Drupal.behaviors.fredericiaFlagSidebar = {
    attach(context) {
      once('fredericia-flag-sidebar', '.flag-sidebar-block', context).forEach((root) => {
        const closeAll = () => {
          root.querySelectorAll('.sidebar-block.is-open').forEach((block) => {
            block.classList.remove('is-open');
            const trigger = block.querySelector('button');
            if (trigger) {
              trigger.setAttribute('aria-expanded', 'false');
            }
          });
        };

        root.querySelectorAll('.sidebar-block > button').forEach((trigger) => {
          trigger.addEventListener('click', (event) => {
            event.preventDefault();
            const block = trigger.parentElement;
            const willOpen = !block.classList.contains('is-open');
            closeAll();
            if (willOpen) {
              block.classList.add('is-open');
              trigger.setAttribute('aria-expanded', 'true');
            }
          });
        });

        root.querySelectorAll('.sidebar-block').forEach((block) => {
          let closeTimer;

          const setHover = (isHovered) => {
            window.clearTimeout(closeTimer);
            if (isHovered) {
              block.classList.add('is-hover');
              return;
            }
            closeTimer = window.setTimeout(() => {
              block.classList.remove('is-hover');
            }, 400);
          };

          block.addEventListener('mouseenter', () => setHover(true));
          block.addEventListener('mouseleave', () => setHover(false));
        });

        document.addEventListener('click', (event) => {
          if (!root.contains(event.target)) {
            closeAll();
          }
        });

        document.addEventListener('keydown', (event) => {
          if (event.key === 'Escape') {
            closeAll();
          }
        });
      });

      once('fredericia-flag-actions', '.flag-actions-source', context).forEach((source) => {
        source.classList.add('flag-actions-bar');
      });
    },
  };
})(Drupal, once);
