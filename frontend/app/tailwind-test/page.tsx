"use client";

export default function ColorTestPage() {
  return (
    <div className="p-8 space-y-8">
      <h1 className="text-3xl font-bold">Tailwind Custom Colors Test</h1>
      <div className="grid grid-cols-3 gap-4">
        <div className="p-4 bg-primary text-primary-foreground">Primary</div>
        <div className="p-4 bg-secondary text-secondary-foreground">
          Secondary
        </div>
        <div className="p-4 bg-accent text-accent-foreground">Accent</div>
        <div className="p-4 bg-muted text-muted-foreground">Muted</div>
        <div className="p-4 bg-success text-success-foreground">Success</div>
        <div className="p-4 bg-warning text-warning-foreground">Warning</div>
        <div className="p-4 bg-destructive text-destructive-foreground">
          Destructive
        </div>
        <div className="p-4 border border-border">Border</div>
      </div>
    </div>
  );
}
