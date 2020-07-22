const MMPokeApp = (function() {
    function _sanitize(string) {
        return string.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ-\s]/gim, '').trim();
    }

    const _domHeaderResize = function() {
        headerElement = document.getElementsByTagName('header')[0];

        if (
            headerElement.className === 'scroll-minimize' &&
            window.pageYOffset < 50 &&
            window.innerWidth > 480
        ) {
            console.log('Remove <header> class');
            headerElement.removeAttribute('class');
        } else if (
            headerElement.className !== 'scroll-minimize' &&
            (window.pageYOffset > 100 || window.innerWidth < 481)
        ) {
            console.log('Add <header> class');
            headerElement.setAttribute('class', 'scroll-minimize');
        }
    };

    const _httpRequirePokeInfos = async function(event) {
        event.preventDefault();

        console.log('START');

        // Get form[input] checked mode
        let modeChecked = [].filter.call(
            document.getElementsByName('pokeapi-mode'),
            (el) => el.checked === true
        );
        // strong string check (local or api)
        mode = modeChecked[0]?.value === 'api' ? 'api' : 'local';

        // Reset the Api url path
        const baseUrl =
            mode === 'api'
                ? 'https://pokeapi.co/api/v2/pokemon/'
                : `${window.location.hostname} -> pokemon -> `;
        let domFullUri = document.getElementById('pokeapi-path');
        domFullUri.innerHTML = baseUrl;

        // Get form[input] name
        let nameInput = document.getElementsByName('pokeapi-name')[0].value;
        nameInput = _sanitize(nameInput);
        document.getElementsByName('pokeapi-name')[0].value = nameInput;
        if (!nameInput) {
            return {
                content:
                    '<div class="box error-bg">Please, enter a valid Pok&eacute;mon name before send your request, thanks!</div>'
            };
        }

        // Disable form[button] temporarily
        event.target.disabled = true;
        event.target.classList.add('button-disabled-wrapper');

        let boxContent = document.getElementById('content');
        boxContent.innerHTML = '<div class="spin-loader"></div>';

        // Get form[input] checked program language
        let plangChecked = [].filter.call(
            document.getElementsByName('pokeapi-plang'),
            (el) => el.checked === true
        );
        // strong check of string and port (by node or php)
        let plang = plangChecked[0]?.value === 'node' ? 'node' : 'php';
        let pport = plang === 'node' ? '5000' : window.location.port;

        // Send the request
        // OPT 1 - Use Json Object
        let dataByJson = {
            name: nameInput,
            metadata: {
                mode: mode,
                serviceWorker: 'serviceWorker' in navigator,
                device:
                    typeof isMobileDevice !== 'function'
                        ? 'unknow'
                        : isMobileDevice()
                        ? 'mobile'
                        : 'desktop',
                display: window.innerWidth + 'x' + window.innerHeight
            }
        };

        // OPT 2 - Use Formdata Object
        // let dataByForm = new FormData();
        // dataByForm.append('name', nameInput);
        // TODO: this field require a cast to Array inside the php render engine
        // dataByForm.append('metadata', JSON.stringify({ plang }));

        let response = await fetch(
            `//${window.location.hostname}:${pport}/${plang}/pokeinfos`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dataByJson) // dataByForm
            }
        );

        // Get the result
        let result = null;
        try {
            // if HTTP-status is 200-299
            result = await response.json();
        } catch (e) {
            console.log('Responce HTTP-Error: ' + response.status);
            console.log(`Error: ${e}`);
            result = {
                path: '',
                content:
                    '<div class="box error-bg">Something was wrong, sorry try again later!</div>'
            };
        } finally {
            boxContent.innerHTML = '';
            // Append path at the Url
            domFullUri.innerHTML = `${baseUrl}${
                result.path
            } <span>(with ${plang})</span>`;
            // Restore the form[button]
            event.target.classList.remove('button-disabled-wrapper');
            event.target.disabled = false;

            console.log('FINISH');

            return result;
        }
    };

    return {
        headerResize: _domHeaderResize,
        getInfos: _httpRequirePokeInfos
    };
})();

// Listeners
window.addEventListener('load', MMPokeApp.headerResize);
window.addEventListener('scroll', MMPokeApp.headerResize);
window.addEventListener('resize', MMPokeApp.headerResize);

const formButtonElement = document.getElementById('pokeapi-send');
if (formButtonElement) {
    formButtonElement.addEventListener('click', async function(event) {
        const result = await MMPokeApp.getInfos(event);
        document.getElementById('content').innerHTML = result.content;
    });
}
