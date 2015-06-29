# altfolio

Requires PHP and json_encode/json_decode. Utilises bootstrap for css
and jquery for additional javascript stuff.

Works with bittrex and cryptsy.

A sample coins.json has been provided. Use the "x" on the webpage
to remove coins.

Use the "Add Trade" at the bottom to add coins.

If the coin already exists, it will re-evaluate the overall rate based on
the latest purchase and its amount.

Ie if your rate is currently 100ABC at 0.00002000 and you buy another 100ABC
at 0.00001000, then the page will show 200ABC at 0.00001500.

