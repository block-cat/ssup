app.initializers.add('block-cat/ssup', () => {
  app.extensionData.for('block-cat-ssup')
    .registerSetting(function () {
      return m('.Form-group', [
          m('label', app.translator.trans("API Token")),
          m('.helpText', app.translator.trans("Token-ul pentru acces prin API")),
          m('input.FormControl', {
              type: 'text',
              bidi: this.setting('block-cat.api_token', ''),
          }),
      ]);
    });
});
