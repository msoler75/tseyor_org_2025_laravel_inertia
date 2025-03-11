<template>
    <component
      :is="tag"
      :class="`share-network-${key}`"
      :href="tag === 'a' ? 'javascript:void(0)' : undefined"
      @click="handleClick"
    >
      <slot />
    </component>
  </template>

  <script setup>

  const props = defineProps({
    network: {
      type: String,
      required: true
    },
    url: String,
    title: String,
    description: {
      type: String,
      default: ''
    },
    quote: {
      type: String,
      default: ''
    },
    hashtags: {
      type: String,
      default: ''
    },
    twitterUser: {
      type: String,
      default: ''
    },
    media: {
      type: String,
      default: ''
    },
    tag: {
      type: String,
      default: 'a'
    },
    popup: {
      type: Object,
      default: () => ({ width: 626, height: 436 })
    }
  })

  const emit = defineEmits(['open', 'close', 'change'])

  const AvailableNetworks = {
  //baidu: 'http://cang.baidu.com/do/add?iu=@u&it=@t',
  //buffer: 'https://bufferapp.com/add?text=@t&url=@u',
  email: 'mailto:?subject=@t&body=@u%0D%0A@d',
  //evernote: 'https://www.evernote.com/clip.action?url=@u&title=@t',
  facebook: 'https://www.facebook.com/sharer/sharer.php?u=@u&title=@t&description=@d&quote=@q&hashtag=@h',
  //flipboard: 'https://share.flipboard.com/bookmarklet/popout?v=2&url=@u&title=@t',
  //hackernews: 'https://news.ycombinator.com/submitlink?u=@u&t=@t',
  //instapaper: 'http://www.instapaper.com/edit?url=@u&title=@t&description=@d',
  //line: 'http://line.me/R/msg/text/?@t%0D%0A@u%0D%0A@d',
  //linkedin: 'https://www.linkedin.com/shareArticle?url=@u',
  messenger: 'fb-messenger://share/?link=@u',
  //odnoklassniki: 'https://connect.ok.ru/dk?st.cmd=WidgetSharePreview&st.shareUrl=@u&st.comments=@t',
  //pinterest: 'https://pinterest.com/pin/create/button/?url=@u&media=@m&description=@t',
  //pocket: 'https://getpocket.com/save?url=@u&title=@t',
  //quora: 'https://www.quora.com/share?url=@u&title=@t',
  reddit: 'https://www.reddit.com/submit?url=@u&title=@t',
  //skype: 'https://web.skype.com/share?url=@t%0D%0A@u%0D%0A@d',
  //sms: 'sms:?body=@t%0D%0A@u%0D%0A@d',
  //stumbleupon: 'https://www.stumbleupon.com/submit?url=@u&title=@t',
  telegram: 'https://t.me/share/url?url=@u&text=@t%0D%0A@d',
  //tumblr: 'https://www.tumblr.com/share/link?url=@u&name=@t&description=@d',
  twitter: 'https://twitter.com/intent/tweet?text=@t&url=@u&hashtags=@h@tu',
  //viber: 'viber://forward?text=@t%0D%0A@u%0D%0A@d',
  //vk: 'https://vk.com/share.php?url=@u&title=@t&description=@d&image=@m&noparse=true',
  //weibo: 'http://service.weibo.com/share/share.php?url=@u&title=@t&pic=@m',
  whatsapp: 'https://api.whatsapp.com/send?text=@t%0D%0A@u%0D%0A@d',
  //wordpress: 'https://wordpress.com/press-this.php?u=@u&t=@t&s=@d&i=@m',
  //xing: 'https://www.xing.com/social/share/spi?op=share&url=@u&title=@t',
  //yammer: 'https://www.yammer.com/messages/new?login=true&status=@t%0D%0A@u%0D%0A@d'
}


  // SSR-safe window reference
  const $window = typeof window !== 'undefined' ? window : null
  const popupWindow = ref(null)
  const popupInterval = ref(null)

  const key = computed(() => props.network.toLowerCase())

  const networks = computed(() =>
    Object.assign({}, AvailableNetworks, props.options?.networks || {})
  )

  const rawLink = computed(() => {
    if (!networks.value[key.value])
      throw new Error(`Network ${key.value} not supported`)

    // iOS SMS handling
    if (key.value === 'sms' && /iphone|ipad/i.test(navigator.userAgent)) {
      return networks.value[key.value].replace(':?', ':&')
    }

    return networks.value[key.value]
  })

  const shareLink = computed(() => {
    let link = rawLink.value

    // Twitter special handling
    if (key.value === 'twitter') {
      if (!props.hashtags) link = link.replace('&hashtags=@h', '')
      if (!props.twitterUser) link = link.replace('@tu', '')
    }

    return link
      .replace(/@tu/g, `&via=${encodeURIComponent(props.twitterUser)}`)
      .replace(/@u/g, encodeURIComponent(props.url))
      .replace(/@t/g, encodeURIComponent(props.title))
      .replace(/@d/g, encodeURIComponent(props.description))
      .replace(/@q/g, encodeURIComponent(props.quote))
      .replace(/@h/g, encodedHashtags.value)
      .replace(/@m/g, encodeURIComponent(props.media))
  })

  const encodedHashtags = computed(() => {
    return key.value === 'facebook' && props.hashtags
      ? `%23${props.hashtags.split(',')[0]}`
      : props.hashtags
  })

  const handleClick = () => {
    rawLink.value.startsWith('http') ? openShareWindow() : openDirectLink()
  }

  const openShareWindow = () => {
    if (!$window) return // SSR guard

    const { width, height } = props.popup
    const { availWidth, availHeight } = $window.screen

    const left = (availWidth - width) / 2
    const top = (availHeight - height) / 2

    popupWindow.value = $window.open(
      shareLink.value,
      `sharer-${key.value}`,
      `width=${width},height=${height},left=${left},top=${top}`
    )

    if (!popupWindow.value) return

    setupPopupWatcher()
    emit('open')
  }

  const setupPopupWatcher = () => {
    popupInterval.value = setInterval(() => {
      if (popupWindow.value?.closed) {
        clearInterval(popupInterval.value)
        popupWindow.value = null
        emit('close')
      }
    }, 500)
  }

  const openDirectLink = () => {
    if (!$window) return
    $window.open(shareLink.value, '_blank')
    emit('open')
  }
  </script>
