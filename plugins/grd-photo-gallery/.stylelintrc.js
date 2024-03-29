module.exports = {
  extends: ['@wordpress/stylelint-config/scss', 'stylelint-config-prettier'],
  rules: {
    'at-rule-empty-line-before': null,
    'declaration-colon-newline-after': null,
    'declaration-no-important': true,
    'function-parentheses-space-inside': null,
    'no-descending-specificity': null,
    'string-quotes': 'single',
    'selector-pseudo-class-parentheses-space-inside': null,
    'rule-empty-line-before': null
  }
}
