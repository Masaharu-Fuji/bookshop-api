
import './bootstrap.js';

/*
* Welcome to your app's main JavaScript file!
*
* This file will be included onto the page via the importmap() Twig function,
* which should already be in your base.html.twig.
*/

import Fetch from './scripts/fetch.js';

const url_graphql = 'https://127.0.0.1:8000/api/graphql/';

const fetch = new Fetch(url_graphql);

fetch.fetch();


