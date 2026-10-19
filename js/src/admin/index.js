import app from 'flarum/admin/app';

app.initializers.add('frozenplain-connect', () => {
    app.extensionData
        .for('frozenplain-connect')
        .registerSetting({
            setting: 'frozenplain-connect.siteUrl',
            type: 'url',
            label: app.translator.trans('frozenplain-connect.admin.site_url_label'),
            help: app.translator.trans('frozenplain-connect.admin.site_url_help'),
        })
        .registerSetting({
            setting: 'frozenplain-connect.publicKey',
            type: 'textarea',
            label: app.translator.trans('frozenplain-connect.admin.public_key_label'),
            help: app.translator.trans('frozenplain-connect.admin.public_key_help'),
        });
});
