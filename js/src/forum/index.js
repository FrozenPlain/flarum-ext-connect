import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import LogInButtons from 'flarum/forum/components/LogInButtons';
import SessionDropdown from 'flarum/forum/components/SessionDropdown';
import LinkButton from 'flarum/common/components/LinkButton';

function siteUrl() {
    return (app.forum.attribute('frozenplainSiteUrl') || '').replace(/\/+$/, '');
}

app.initializers.add('frozenplain-connect', () => {
    // LogInButtons sits at the top of both the log-in and sign-up modals
    extend(LogInButtons.prototype, 'items', function (items) {
        if (!siteUrl()) return;

        const href = `${siteUrl()}/forum/connect?return=${encodeURIComponent(window.location.href)}`;

        items.add(
            'frozenplain',
            m('.FrozenPlainConnect', [
                LinkButton.component(
                    {
                        className: 'Button Button--primary Button--block FrozenPlainConnect-button',
                        icon: 'fas fa-snowflake',
                        href,
                        external: true,
                    },
                    app.translator.trans('frozenplain-connect.forum.button'),
                ),
                m('p.FrozenPlainConnect-hint', app.translator.trans('frozenplain-connect.forum.hint')),
                m('.FrozenPlainConnect-or', m('span', app.translator.trans('frozenplain-connect.forum.or'))),
            ]),
            100,
        );
    });

    // Signed in through frozenplain.com: the forum's own log-out can't clear that cookie,
    // so sign out on frozenplain.com, which then comes back here
    extend(SessionDropdown.prototype, 'items', function (items) {
        if (!app.forum.attribute('frozenplainSession') || !siteUrl() || !items.has('logOut')) return;

        items.setContent(
            'logOut',
            LinkButton.component(
                {
                    icon: 'fas fa-sign-out-alt',
                    href: `${siteUrl()}/logout?return=forum`,
                    external: true,
                },
                app.translator.trans('core.forum.header.log_out_button'),
            ),
        );
    });
});
