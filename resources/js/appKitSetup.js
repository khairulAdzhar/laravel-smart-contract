// resources/js/appKitSetup.js
import { createAppKit } from '@reown/appkit/react'
import { EthersAdapter } from '@reown/appkit-adapter-ethers'
import { arbitrum, mainnet, sepolia } from '@reown/appkit/networks'

export const appKit = createAppKit({
  adapters: [new EthersAdapter()],
  networks: [arbitrum, mainnet, sepolia],
  projectId: '3555b62326bd83cf91146427f3a9a340', // ganti dengan projectId Anda
  metadata: {
    name: 'My Laravel App',
    description: 'Laravel WalletConnect Integration',
    url: 'https://my-laravel-app.com',
    icons: ['https://my-laravel-app.com/icon.png'],
  },
  features: {
    analytics: true,
  },
})
