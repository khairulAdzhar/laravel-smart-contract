import { createAppKit } from '@reown/appkit/react'
import { EthersAdapter } from '@reown/appkit-adapter-ethers'
import { arbitrum, mainnet, sepolia } from '@reown/appkit/networks'

// Konfigurasi AppKit
const projectId = '3555b62326bd83cf91146427f3a9a340'
const networks = [arbitrum, mainnet,sepolia]

const metadata = {
  name: 'My Laravel App',
  description: 'Laravel WalletConnect Integration',
  url: 'https://my-laravel-app.com',
  icons: ['https://my-laravel-app.com/icon.png']
}

createAppKit({
  adapters: [new EthersAdapter()],
  networks,
  metadata,
  projectId,
  features: {
    analytics: true
  }
})
