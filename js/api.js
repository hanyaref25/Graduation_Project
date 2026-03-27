window.APP_CONFIG = {
  apiBaseUrl: 'http://127.0.0.1:8000/api.php'
};

window.apiGet = async function apiGet(path) {
  var response = await fetch(window.APP_CONFIG.apiBaseUrl + path, {
    headers: {
      Accept: 'application/json'
    }
  });

  if (!response.ok) {
    throw new Error('Request failed with status ' + response.status);
  }

  return response.json();
};

window.apiPost = async function apiPost(path, body) {
  var response = await fetch(window.APP_CONFIG.apiBaseUrl + path, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json'
    },
    body: JSON.stringify(body)
  });

  if (!response.ok) {
    var errorPayload = null;

    try {
      errorPayload = await response.json();
    } catch (error) {
      errorPayload = null;
    }

    throw new Error(
      (errorPayload && errorPayload.message) || ('Request failed with status ' + response.status)
    );
  }

  return response.json();
};
