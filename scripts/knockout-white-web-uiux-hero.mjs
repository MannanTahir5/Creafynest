/**
 * Removes a light / white matte around the hero PNG by flood-filling from the
 * image edges into pixels that match the average edge color (within tolerance).
 */
import sharp from 'sharp'
import path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const root = path.join(__dirname, '..')
const target = path.join(root, 'resources/js/assets/web-uiux-hero.png')

function dist(a, b) {
  const dr = a[0] - b[0]
  const dg = a[1] - b[1]
  const db = a[2] - b[2]
  return Math.sqrt(dr * dr + dg * dg + db * db)
}

function idx(x, y, width, channels) {
  return (y * width + x) * channels
}

async function main() {
  const { data, info } = await sharp(target).ensureAlpha().raw().toBuffer({ resolveWithObject: true })
  const { width, height, channels } = info

  const samples = []
  for (let x = 0; x < width; x++) {
    for (const y of [0, height - 1]) {
      const i = idx(x, y, width, channels)
      samples.push([data[i], data[i + 1], data[i + 2]])
    }
  }
  for (let y = 0; y < height; y++) {
    for (const x of [0, width - 1]) {
      const i = idx(x, y, width, channels)
      samples.push([data[i], data[i + 1], data[i + 2]])
    }
  }

  const edgeRef = [
    samples.reduce((s, c) => s + c[0], 0) / samples.length,
    samples.reduce((s, c) => s + c[1], 0) / samples.length,
    samples.reduce((s, c) => s + c[2], 0) / samples.length,
  ]

  const tolerance = 42

  function nearMatte(rgb) {
    return dist(rgb, edgeRef) < tolerance
  }

  const out = Buffer.from(data)
  const seen = new Uint8Array(width * height)
  const q = []

  function push(x, y) {
    if (x < 0 || x >= width || y < 0 || y >= height) return
    const qi = y * width + x
    if (seen[qi]) return
    const i = idx(x, y, width, channels)
    const rgb = [out[i], out[i + 1], out[i + 2]]
    if (!nearMatte(rgb)) return
    seen[qi] = 1
    q.push([x, y])
  }

  for (let x = 0; x < width; x++) {
    push(x, 0)
    push(x, height - 1)
  }
  for (let y = 0; y < height; y++) {
    push(0, y)
    push(width - 1, y)
  }

  while (q.length) {
    const [x, y] = q.shift()
    const i = idx(x, y, width, channels)
    out[i + 3] = 0
    const neighbors = [
      [x + 1, y],
      [x - 1, y],
      [x, y + 1],
      [x, y - 1],
    ]
    for (const [nx, ny] of neighbors) push(nx, ny)
  }

  await sharp(out, { raw: { width, height, channels } })
    .png({ compressionLevel: 9 })
    .toFile(target)

  console.log('Updated', target, '(edge matte removed, ref RGB ~', edgeRef.map((n) => n.toFixed(1)).join(', '), ')')
}

main().catch((e) => {
  console.error(e)
  process.exit(1)
})
