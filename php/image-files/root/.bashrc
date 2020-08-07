cat <<'MSG'
                ____  __.         .___                 __                   __
__  _  __ ____ |    |/ _|____   __| _/____           _/  |_  ____   _______/  |_
\ \/ \/ // __ \|      < /  _ \ / __ |/ __ \   ______ \   __\/ __ \ /  ___/\   __\
 \     /\  ___/|    |  (  <_> ) /_/ \  ___/  /_____/  |  | \  ___/ \___ \  |  |
  \/\_/  \___  >____|__ \____/\____ |\___  >          |__|  \___  >____  > |__|
             \/        \/          \/    \/                     \/     \/

MSG

echo "PHP version: ${PHP_VERSION}"

if ! shopt -oq posix; then
  if [ -f /usr/share/bash-completion/bash_completion ]; then
    . /usr/share/bash-completion/bash_completion
  elif [ -f /etc/bash_completion.d/yii ]; then
    . /etc/bash_completion.d/yii
  fi
fi
