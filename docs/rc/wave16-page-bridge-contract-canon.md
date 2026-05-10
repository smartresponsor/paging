# Wave 16 — Page Bridge Contract Canonization

Wave 16 strengthens the existing Page output boundary by introducing an explicit `Bridge` namespace.

## Added

- `DTO/Bridge/PageBridgePayload`
- `DTO/Bridge/PageBridgeAttachment`
- `DTO/Bridge/PageBridgeRenderHints`
- `DTO/Bridge/PageBridgeLegalNotice`
- `ServiceInterface/Bridge/PageBridgePayloadFactoryInterface`
- `ServiceInterface/Bridge/PageBridgeContractProviderInterface`
- `Service/Bridge/PageBridgePayloadFactory`
- `Service/Bridge/PageBridgeContractProvider`
- `Command/PageBridgeContractCommand`
- `docs/bridge/page-interfacing-bridge-contract.md`
- `tools/smoke/page-bridge-contract-smoke.ps1`

## Canon

Paging exposes Page bridge contracts. Interfacing consumes bridge payloads. Interfacing must not consume Doctrine Page entities as its visual rendering boundary.

This wave does not add EasyAdmin, CMS modules, SEO ownership, locale ownership, or attachment storage.
