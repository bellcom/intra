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

      once('fredericia-flag-actions', '.node--view-mode-full', context).forEach((node) => {
        const flags = node.querySelectorAll('.flag-bookmarks, .flag-shortcut, .flag-unread');
        if (!flags.length) {
          return;
        }

        let bar = document.querySelector('.flag-actions-bar');
        if (!bar) {
          bar = document.createElement('div');
          bar.className = 'flag-actions-bar';
          document.body.appendChild(bar);
        }

        flags.forEach((flag) => {
          bar.appendChild(flag);
        });
      });
    },
  };
})(Drupal, once);
