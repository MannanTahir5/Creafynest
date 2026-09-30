/**
 * Unified service catalog — design, AI, video, and development (shared JSON with PHP ServicesCatalog).
 */
import designCatalog from '../../../database/data/design-services-catalog.json'
import aiCatalog from '../../../database/data/ai-services-catalog.json'
import videoCatalog from '../../../database/data/video-services-catalog.json'

/**
 * @param {typeof designCatalog.categories[0]} cat
 */
export function toPremiumCategory(cat) {
  return {
    slug: cat.slug,
    title: cat.title,
    subtitle: cat.subtitle,
    featureIcon: cat.feature_icon,
    items: cat.services.map((s) => ({
      id: s.slug,
      icon: s.icon,
      title: s.title,
      description: s.description,
      service_slug: s.slug,
    })),
  }
}

function toMegaColumns(categories) {
  return categories.map((cat) => ({
    id: cat.slug,
    title: cat.title,
    color: cat.color,
    items: cat.services.map((s) => ({
      icon: s.icon,
      title: s.title,
      description: s.description,
      href: `/services/${s.slug}`,
    })),
  }))
}

export const designServiceCategories = designCatalog.categories
export const aiServiceCategories = aiCatalog.categories
export const videoServiceCategories = videoCatalog.categories

export const designMegaMenuColumns = toMegaColumns(designCatalog.categories)
export const aiMegaMenuColumns = toMegaColumns(aiCatalog.categories)
export const videoMegaMenuColumns = toMegaColumns(videoCatalog.categories)

export const developmentMegaMenuColumns = designCatalog.development_categories

export const servicesMegaMenuSections = [
  { id: 'design', label: 'Design Services', columns: designMegaMenuColumns },
  { id: 'ai', label: 'AI Services', columns: aiMegaMenuColumns },
  { id: 'video', label: 'Video & Animation', columns: videoMegaMenuColumns },
  { id: 'development', label: 'Software Development', columns: developmentMegaMenuColumns },
]

export const servicesMegaMenuColumns = [
  ...designMegaMenuColumns,
  ...aiMegaMenuColumns,
  ...videoMegaMenuColumns,
  ...developmentMegaMenuColumns,
]

const developmentPremiumCategories = [
  {
    slug: 'web-development',
    title: 'Web Development',
    subtitle: 'Custom websites, web applications, and digital platforms.',
    featureIcon: 'LayoutGrid',
    items: [
      { id: 'web-ui', icon: 'LayoutTemplate', title: 'Website UI/UX', description: 'Responsive, user-centered interfaces with accessible patterns and design systems.', service_slug: 'website-ui-ux' },
      { id: 'cms', icon: 'FileStack', title: 'CMS', description: 'Structured content, editorial workflows, and fast delivery for marketing teams.', service_slug: 'cms-solutions' },
      { id: 'custom-web', icon: 'Box', title: 'Custom Web Application', description: 'Dashboards, internal tools, and multi-tenant products built to scale with your business.', service_slug: 'custom-web-applications' },
      { id: 'ecommerce', icon: 'ShoppingCart', title: 'E-commerce', description: 'Catalogs, checkout, payments, and fulfillment-aware storefront experiences.', service_slug: 'ecommerce-development' },
    ],
  },
  {
    slug: 'mobile-development',
    title: 'Mobile App Development',
    subtitle: 'Native-quality experiences across iOS, Android, and cross-platform stacks.',
    featureIcon: 'Smartphone',
    items: [
      { id: 'mobile-ui', icon: 'Sparkles', title: 'Mobile App UI/UX', description: 'Motion, gestures, and polish tuned for small screens and real-world performance.', service_slug: 'mobile-app-ui-ux' },
      { id: 'android', icon: 'Smartphone', title: 'Android App Development', description: 'Material-aware builds, Play compliance, and solid offline-first patterns.', service_slug: 'android-app-development' },
      { id: 'ios', icon: 'TabletSmartphone', title: 'iOS App Development', description: 'Human Interface Guidelines–friendly flows with App Store–ready delivery.', service_slug: 'ios-app-development' },
      { id: 'cross', icon: 'Share2', title: 'Cross-Platform Development', description: 'One codebase where it makes sense; native modules where it matters.', service_slug: 'cross-platform-app-development' },
    ],
  },
  {
    slug: 'ai-solutions',
    title: 'AI Solutions',
    subtitle: 'Practical AI that augments your product without compromising trust or clarity.',
    featureIcon: 'Bot',
    items: [
      { id: 'gen-ai', icon: 'Sparkles', title: 'Generative AI', description: 'Assistants, summarization, and content workflows grounded in your data policies.', service_slug: 'generative-ai' },
      { id: 'voice', icon: 'Mic', title: 'Voice AI', description: 'Speech interfaces, transcription, and conversational UX with clear fallbacks.', service_slug: 'voice-ai' },
      { id: 'custom-model', icon: 'Cpu', title: 'Custom Model Integration', description: 'Inference pipelines, evaluation harnesses, and cost-aware deployment.', service_slug: 'custom-ai-model-integration' },
      { id: 'rag', icon: 'Database', title: 'RAG & retrieval', description: 'Retrieval-augmented answers with citations, guardrails, and observability.', service_slug: 'rag-retrieval-systems' },
    ],
  },
  {
    slug: 'digital-marketing',
    title: 'Digital Marketing',
    subtitle: 'Technical foundations that make acquisition and attribution actually measurable.',
    featureIcon: 'LineChart',
    items: [
      { id: 'seo', icon: 'Search', title: 'SEO', description: 'Technical SEO, structured data, and performance budgets that search engines reward.', service_slug: 'seo-services' },
      { id: 'social', icon: 'Share2', title: 'Social Media Marketing', description: 'Campaign-ready landing pages, tracking, and creative iteration loops.', service_slug: 'social-media-marketing' },
      { id: 'ppc', icon: 'Target', title: 'PPC', description: 'Conversion tracking, experiments, and landing experiences aligned to bids.', service_slug: 'ppc-advertising' },
      { id: 'content', icon: 'PenLine', title: 'Content Writing', description: 'Clear product narrative, docs, and on-site copy that supports discovery.', service_slug: 'content-writing' },
    ],
  },
]

export const premiumServiceCategories = [
  ...designCatalog.categories.map(toPremiumCategory),
  ...aiCatalog.categories.map(toPremiumCategory),
  ...videoCatalog.categories.map(toPremiumCategory),
  ...developmentPremiumCategories,
]
