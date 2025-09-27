# Aliases for Artisan
alias art='php artisan'
alias a='php artisan'

# Aliases for Pest
alias pest=./vendor/bin/pest
alias p=./vendor/bin/pest
alias pp='./vendor/bin/pest --parallel'
alias pps='./vendor/bin/pest --parallel --stop-on-failure'
alias ppr='./vendor/bin/pest --parallel --order-by random'
alias pr='./vendor/bin/pest --order-by random'
alias pf='./vendor/bin/pest --filter'
alias pg='./vendor/bin/pest --group='
alias pc='./vendor/bin/pest --coverage'
alias prg='./vendor/bin/pest --order-by random --group='
alias ctu='composer test:unit'
alias ctuc='composer test:unit:co'
alias ct='composer test'

# Stop executing your test suite upon encountering
# the first failure or error.
alias pb='./vendor/bin/pest --bail'

# Only run tests that have uncommitted
# changes according to Git
alias pd='./vendor/bin/pest --dirty'

alias xoff='unset XDEBUG_SESSION; export XDEBUG_MODE=off; echo "xDebug DEBUG mode is now OFF"'
alias xon='export XDEBUG_SESSION=1; export XDEBUG_MODE=debug; export PHP_IDE_CONFIG="serverName=channels.work"; echo "xDebug DEBUG mode is now ON"'
alias xc='unset XDEBUG_SESSION; export XDEBUG_MODE=coverage; echo "xDebug COVERAGE mode is now ON"'

alias cda='composer dump-autoload'

# Reset the opcache
alias rop='php -r opcache_reset();'

alias ..='cd ..'
alias ...='cd ../..'
alias ....='cd ../../..'
alias .....='cd ../../../..'
