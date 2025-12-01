(function () {
  "use strict";

  // Wait until DOM ready
  document.addEventListener("DOMContentLoaded", function () {
    const settings =
      window.CAC_CHAT && window.CAC_CHAT.settings
        ? window.CAC_CHAT.settings
        : {
            position: "right",
            vertical_offset: 120,
            avatar_collapsed: "",
            avatar_expanded: "",
          };

    // Create widget root
    const root = document.createElement("div");
    root.className =
      "cac-widget-root cac-position-" + (settings.position || "right");
    root.style.bottom = (settings.vertical_offset || 120) + "px";

    // Collapsed floating button
    const collapsed = document.createElement("div");
    collapsed.className = "cac-collapsed";

    const avatarImg = document.createElement("div");
    avatarImg.className = "cac-avatar";
    if (settings.avatar_collapsed) {
      avatarImg.style.backgroundImage =
        'url("' + settings.avatar_collapsed + '")';
    } else {
      // fallback gradient
      avatarImg.classList.add("cac-fallback-gradient");
    }
    collapsed.appendChild(avatarImg);

    const talkBtn = document.createElement("button");
    talkBtn.className = "cac-talk-btn";
    talkBtn.type = "button";
    talkBtn.innerHTML =
      '<span class="cac-phone">📞</span><span class="cac-text">Talk to AI</span>';
    collapsed.appendChild(talkBtn);

    root.appendChild(collapsed);

    // Expanded chat card
    const expanded = document.createElement("div");
    expanded.className = "cac-expanded";
    expanded.setAttribute("aria-hidden", "true");

    expanded.innerHTML = `
			<div class="cac-expanded-inner">
				<div class="cac-header">
					<div class="cac-header-avatar"></div>
					<div class="cac-header-title">AI Receptionist</div>
					<button class="cac-close" aria-label="Close">✕</button>
				</div>
				<div class="cac-messages" role="log" aria-live="polite"></div>
				<div class="cac-input">
					<input type="text" class="cac-input-field" placeholder="Send a message" aria-label="Send a message" />
					<button class="cac-send">Send</button>
				</div>
			</div>
		`;

    // Set expanded avatar
    const headerAvatar = expanded.querySelector(".cac-header-avatar");
    if (settings.avatar_expanded) {
      headerAvatar.style.backgroundImage =
        'url("' + settings.avatar_expanded + '")';
    } else if (settings.avatar_collapsed) {
      headerAvatar.style.backgroundImage =
        'url("' + settings.avatar_collapsed + '")';
    } else {
      headerAvatar.classList.add("cac-fallback-gradient");
    }

    root.appendChild(expanded);
    document.body.appendChild(root);

    // Toggle handlers
    function openChat() {
      expanded.setAttribute("aria-hidden", "false");
      root.classList.add("cac-open");
      inputField.focus();
    }
    function closeChat() {
      expanded.setAttribute("aria-hidden", "true");
      root.classList.remove("cac-open");
    }

    talkBtn.addEventListener("click", openChat);
    root.querySelector(".cac-close").addEventListener("click", closeChat);

    // Messages handling
    const messagesEl = expanded.querySelector(".cac-messages");
    const inputField = expanded.querySelector(".cac-input-field");
    const sendBtn = expanded.querySelector(".cac-send");

    function appendMessage(text, who) {
      const el = document.createElement("div");
      el.className = "cac-message cac-" + (who === "user" ? "user" : "bot");
      el.textContent = text;
      messagesEl.appendChild(el);
      messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // Send message to rest endpoint
    async function sendMessage() {
      const text = inputField.value.trim();
      if (!text) {
        return;
      }
      appendMessage(text, "user");
      inputField.value = "";

      // Show loading placeholder
      const loading = document.createElement("div");
      loading.className = "cac-message cac-bot cac-loading";
      loading.textContent = "...";
      messagesEl.appendChild(loading);
      messagesEl.scrollTop = messagesEl.scrollHeight;

      try {
        const res = await fetch(window.CAC_CHAT.rest_url, {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "Content-Type": "application/json",
            "X-WP-Nonce": window.CAC_CHAT.nonce,
          },
          body: JSON.stringify({ message: text }),
        });

        const data = await res.json();

        // Remove loading
        loading.remove();

        // Try to determine the bot text. This depends on your webhook's response format.
        let botText = "";
        if (data && typeof data === "object") {
          if (data.reply) {
            botText = data.reply;
          } else if (data.message) {
            botText = data.message;
          } else if (data.response) {
            // If webhook returned { response: "..." } or raw body forwarded
            if (typeof data.response === "string") {
              botText = data.response;
            } else if (data.response.text) {
              botText = data.response.text;
            }
          } else {
            // fallback: stringify
            botText = JSON.stringify(data);
          }
        } else {
          botText = String(data);
        }

        appendMessage(botText, "bot");
      } catch (err) {
        loading.remove();
        appendMessage("Error: " + (err.message || "Request failed"), "bot");
      }
    }

    sendBtn.addEventListener("click", sendMessage);
    inputField.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        sendMessage();
      }
    });

    // Close on outside click
    document.addEventListener("click", function (e) {
      if (!root.contains(e.target) && root.classList.contains("cac-open")) {
        closeChat();
      }
    });
  });
})();
