# OpenCart Payment Gateway

## Requirements

- OpenCart 3.x
- Merchant ID, Production & Test Keys
- Production & Test Gateway URLs

## Installation

Download the extension from [here](https://github.com/PaycomUZ/opencart-payment-gateway/releases/latest).

Sign into your OpenCart's admin panel.

Go to the `Extensions \ Installer` page.

Upload the downloaded ZIP archive.

Progress bar should become green to indicate successful upload.

![Upload the Extension](images/01_upload_and_install.png)

Then go to the `Extensions \ Extensions` page and set the extension type filter as `Payments`.

![Filter Payments](images/02_filter_by_extension_type.png)

Scroll the page little bit down and find the `KiT Payme` extension. 
Install it by clicking the green `+` button located on that line.

![Install the Extension](images/03_install_the_plugin.png)

After successful installation green `+` button becomes red `-`, which removes the extension on click.

Now the extension is installed successfully, but not configured yet.

Click the blue pencil button to open configuration page of the extension.

![Open Configuration Page](images/04_go_to_settings.png)

In the opened page configure the extension as depicted in the following screenshot, but using own parameters.
Save the parameters.

![Configure the Extension](images/05_set_parameters_and_enable.png)

Also set the `Endpoint URL` of the online cashdesk in the `Merchants Cabinet`.

Now extension should become `Enabled` and ready to use.

![Extension is Enabled](images/06_enabled_and_ready_to_work.png)

From now on the users can choose `Payme` as a payment method on the `Checkout` page.

![Choose Payme](images/07_ability_to_choose_Payme.png)
